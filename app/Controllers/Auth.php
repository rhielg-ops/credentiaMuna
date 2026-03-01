<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\LoginAttemptModel;
use App\Models\AccountLockModel;
use App\Models\VerificationCodeModel;
use App\Models\ActivityLogModel;
use App\Libraries\EmailService;

class Auth extends BaseController
{
    protected $userModel;
    protected $loginAttemptModel;
    protected $accountLockModel;
    protected $verificationCodeModel;
    protected $activityLogModel;
    protected $emailService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->loginAttemptModel = new LoginAttemptModel();
        $this->accountLockModel = new AccountLockModel();
        $this->verificationCodeModel = new VerificationCodeModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->emailService = new EmailService();
    }

    /**
     * Show login page
     */
    public function index()
    {
        // If already logged in, redirect to appropriate dashboard
        if (session()->get('logged_in')) {
            return $this->redirectToDashboard();
        }

        $data = [];

        // AuthFilter destroys session of deactivated user and sets this flashdata
        if (session()->getFlashdata('deactivated')) {
            $data['show_deactivated_modal'] = true;
        }

        return view('auth/login', $data);
    }

    /**
     * Handle login (Step 1: Password verification)
     * Now accepts EITHER email OR username
     */
    public function login()
    {
        $emailOrUsername = $this->request->getPost('email'); // Field name is 'email' but accepts both
        $password = $this->request->getPost('password');

        // Validate input
        if (!$emailOrUsername || !$password) {
            return redirect()->back()->with('error', 'Please enter your email/username and password.');
        }

        // Find user by email OR username
        $user = $this->userModel
            ->groupStart()
                ->where('email', $emailOrUsername)
                ->orWhere('username', $emailOrUsername)
            ->groupEnd()
            ->first();

        // Determine email for lock checking
        if ($user) {
            $email = $user['email'];
        } else {
            // Use the input if it looks like an email, otherwise construct a temp one for logging
            $email = filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL) 
                ? $emailOrUsername 
                : $emailOrUsername . '@username.login';
        }

        // Check if account is locked
        if ($this->accountLockModel->isAccountLocked($email, 'password_attempts')) {
            $remainingTime = $this->accountLockModel->getRemainingLockTime($email, 'password_attempts');
            $minutes = ceil($remainingTime / 60);
            
            return redirect()->back()->with('error', "Account is locked due to multiple failed attempts. Try again in {$minutes} minute(s).");
        }

        // Check if user exists
        if (!$user) {
            $this->loginAttemptModel->recordAttempt($email, 'password', false);
            return redirect()->back()->with('error', 'Invalid email/username or password.');
        }

         // Inactive users see a modal, not a plain error
        if ($user['status'] === 'inactive') {
            $approvalModel = new \App\Models\ApprovalRequestModel();
            $hasPending = (bool) $approvalModel
                ->where('user_id', $user['user_id'])
                ->where('status', 'pending')
                ->first();
            return view('auth/login', [
                'deactivated_user_id' => $user['user_id'],
                'deactivated_email'   => $user['email'],
                'deactivated_name'    => $user['full_name'],
                'has_pending_request' => $hasPending,
            ]);
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            // Record failed attempt
            $this->loginAttemptModel->recordAttempt($email, 'password', false);
            
            // Check failed attempts count
            $failedAttempts = $this->loginAttemptModel->getFailedAttemptsCount($email, 'password', 30);
            
            // Get max attempts from settings
            $maxAttempts = 5; // Default, can be loaded from system_settings
            
            if ($failedAttempts >= $maxAttempts) {
                // Lock account
                $this->accountLockModel->lockAccount($email, 'password_attempts', 5);
                
                // Send notification email
                $unlockTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                $this->emailService->sendAccountLockedEmail($email, $user['full_name'], $unlockTime);
                
                // Log activity
                $this->activityLogModel->logActivity($user['user_id'], 'account_locked', 'Account locked due to failed password attempts');
                
                return redirect()->back()->with('error', 'Too many failed attempts. Your account has been locked for 5 minutes.');
            }
            
            $attemptsLeft = $maxAttempts - $failedAttempts;
            return redirect()->back()->with('error', "Invalid email/username or password. You have {$attemptsLeft} attempt(s) remaining.");
        }

        // Password is correct - record successful attempt
        $this->loginAttemptModel->recordAttempt($email, 'password', true);

        // Generate and send verification code
        $code = $this->verificationCodeModel->generateCode($user['user_id'],  $email, 10);
        
        if (!$code) {
            return redirect()->back()->with('error', 'Failed to generate verification code. Please try again.');
        }

        // Send verification code via email
        $emailSent = $this->emailService->sendVerificationCode($email, $user['full_name'], $code);
        
        if (!$emailSent) {
            log_message('error', 'Failed to send verification code to: ' . $email);
        }

        // Store user info in session temporarily (not fully logged in yet)
        session()->set([
            'temp_user_id' => $user['user_id'],
            'temp_email' => $email,
            'temp_full_name' => $user['full_name'],
            'temp_role' => $user['role'],
            'awaiting_2fa' => true
        ]);

        // Log activity
        $this->activityLogModel->logActivity($user['user_id'], 'password_verified', 'Password verified, awaiting 2FA');

        // Redirect to verification page
        return redirect()->to('/auth/verify-code');
    }

    /**
     * Show verification code page
     */
    public function verifyCodePage()
    {
        // Check if user is awaiting 2FA
        if (!session()->get('awaiting_2fa')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $data = [
            'email' => session()->get('temp_email'),
            'full_name' => session()->get('temp_full_name')
        ];

        return view('auth/verify_code', $data);
    }

    /**
     * Verify 2FA code (Step 2: Code verification)
     */
    public function verifyCode()
    {
        // Check if user is awaiting 2FA
        if (!session()->get('awaiting_2fa')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $email = session()->get('temp_email');
        $userId = session()->get('temp_user_id');
        $code = $this->request->getPost('code');

        // Validate input
        if (!$code || strlen($code) !== 6) {
            return redirect()->back()->with('error', 'Please enter a valid 6-digit code.');
        }

        // Check if account is locked for code attempts
        if ($this->accountLockModel->isAccountLocked($email, 'code_attempts')) {
            $remainingTime = $this->accountLockModel->getRemainingLockTime($email, 'code_attempts');
            $minutes = ceil($remainingTime / 60);
            
            return redirect()->back()->with('error', "Too many failed code attempts. Try again in {$minutes} minute(s).");
        }

        // Verify code
        if (!$this->verificationCodeModel->verifyCode($email, $code)) {
            // Record failed attempt
            $this->loginAttemptModel->recordAttempt($email, 'verification_code', false);
            
            // Check failed attempts count
            $failedAttempts = $this->loginAttemptModel->getFailedAttemptsCount($email, 'verification_code', 30);
            
            $maxAttempts = 5;
            
            if ($failedAttempts >= $maxAttempts) {
                // Lock account
                $this->accountLockModel->lockAccount($email, 'code_attempts', 5);
                
                // Get user for notification
                $user = $this->userModel->find($userId);
                $unlockTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                $this->emailService->sendAccountLockedEmail($email, $user['full_name'], $unlockTime);
                
                // Log activity
                $this->activityLogModel->logActivity($userId, 'account_locked', 'Account locked due to failed code attempts');
                
                // Clear temp session
                $this->clearTempSession();
                
                return redirect()->to('/login')->with('error', 'Too many failed code attempts. Your account has been locked for 5 minutes.');
            }
            
            $attemptsLeft = $maxAttempts - $failedAttempts;
            return redirect()->back()->with('error', "Invalid verification code. You have {$attemptsLeft} attempt(s) remaining.");
        }

        // Code is correct - record successful attempt
        $this->loginAttemptModel->recordAttempt($email, 'verification_code', true);

        // Get user data
        $user = $this->userModel->find($userId);

        // Update last login
        $this->userModel->updateLastLogin($userId);

        // Set full session (now fully logged in)
        session()->set([
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'username' => $user['username'] ?? null,
            'full_name' => $user['full_name'],
            'role' => $user['role'],
            'access_level' => $user['access_level'],
            'staff_unit' => $user['staff_unit'] ?? null,
            'logged_in' => true
        ]);

        // Clear temp session
        $this->clearTempSession();

        // Log activity
        $this->activityLogModel->logActivity($userId, 'login_success', 'Successfully logged in with 2FA');

        // Redirect to appropriate dashboard
        return $this->redirectToDashboard();
    }

    /**
     * Resend verification code
     */
    public function resendCode()
    {
        if (!session()->get('awaiting_2fa')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid session.']);
        }

        $email = session()->get('temp_email');
        $userId = session()->get('temp_user_id');

        // Generate new code
        $code = $this->verificationCodeModel->generateCode($userId, $email, 10);
        
        if (!$code) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to generate code.']);
        }

        // Get user
        $user = $this->userModel->find($userId);

        // Send email
        $emailSent = $this->emailService->sendVerificationCode($email, $user['full_name'], $code);
        
        if ($emailSent) {
            $this->activityLogModel->logActivity($userId, 'code_resent', 'Verification code resent');
            return $this->response->setJSON(['success' => true, 'message' => 'Verification code resent successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to send email.']);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        // Log activity before destroying session
        if (session()->get('user_id')) {
            $this->activityLogModel->logActivity(session()->get('user_id'), 'logout', 'User logged out');
        }

        // Destroy session
        session()->destroy();

        // Redirect to login
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Clear temporary session data
     */
    protected function clearTempSession()
    {
        session()->remove(['temp_user_id', 'temp_email', 'temp_full_name', 'temp_role', 'awaiting_2fa']);
    }

    /**
     * Redirect to appropriate dashboard based on role
     */
    protected function redirectToDashboard()
    {
        $role = session()->get('role');

        if ($role === 'admin') {
            return redirect()->to('/super-admin/dashboard');
        } else {
            return redirect()->to('/dashboard');
        }
    }

    /**
     * AJAX: deactivated user submits a reactivation request
     * Route: POST auth/send-approval-request
     */
    public function sendApprovalRequest()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false]);
        }

        $userId   = (int) $this->request->getPost('user_id');
        $email    = $this->request->getPost('email');
        $fullName = $this->request->getPost('full_name');

        if (!$userId || !$email) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.']);
        }

        $user = $this->userModel->find($userId);
        if (!$user || $user['status'] !== 'inactive') {
            return $this->response->setJSON(['success' => false, 'message' => 'Account not found or already active.']);
        }

        $approvalModel = new \App\Models\ApprovalRequestModel();

        $existing = $approvalModel->where('user_id', $userId)->where('status', 'pending')->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'You already have a pending request.']);
        }

        $approvalModel->createRequest($userId, $email, $fullName, 'reactivation', 'User requesting account reactivation');

        // Email all active admins
        $admins = $this->userModel->where('role', 'admin')->where('status', 'active')->findAll();
        foreach ($admins as $admin) {
            try {
                $this->emailService->sendReactivationRequestEmail(
                    $admin['email'],
                    $admin['full_name'],
                    $fullName,
                    $email
                );
            } catch (\Exception $e) {
                log_message('error', 'Failed to notify admin: ' . $e->getMessage());
            }
        }

        $this->activityLogModel->logActivity($userId, 'reactivation_requested', 'User submitted reactivation request');

        return $this->response->setJSON(['success' => true, 'message' => 'Your request has been sent to the administrator.']);
    }
}
