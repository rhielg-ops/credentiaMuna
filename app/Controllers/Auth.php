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

        return view('auth/login');
    }

    /**
     * Handle login (Step 1: Password verification)
     */
    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validate input
        if (!$email || !$password) {
            return redirect()->back()->with('error', 'Please enter both email and password.');
        }

        // Check if account is locked
        if ($this->accountLockModel->isAccountLocked($email, 'password_attempts')) {
            $lock = $this->accountLockModel->getActiveLock($email, 'password_attempts');
            $remainingTime = $this->accountLockModel->getRemainingLockTime($email, 'password_attempts');
            $minutes = ceil($remainingTime / 60);
            
            return redirect()->back()->with('error', "Account is locked due to multiple failed attempts. Try again in {$minutes} minute(s).");
        }

        // Check if user exists
        $user = $this->userModel->where('email', $email)->first();
        
        if (!$user) {
            $this->loginAttemptModel->recordAttempt($email, 'password', false);
            return redirect()->back()->with('error', 'Invalid email or password.');
        }

        // Check account status
        if ($user['status'] !== 'active') {
            return redirect()->back()->with('error', 'Your account is not active. Please contact your administrator.');
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
                $this->activityLogModel->logActivity($user['id'], 'account_locked', 'Account locked due to failed password attempts');
                
                return redirect()->back()->with('error', 'Too many failed attempts. Your account has been locked for 5 minutes.');
            }
            
            $attemptsLeft = $maxAttempts - $failedAttempts;
            return redirect()->back()->with('error', "Invalid email and password. You have {$attemptsLeft} attempt(s) remaining.");
        }

        // Password is correct - record successful attempt
        $this->loginAttemptModel->recordAttempt($email, 'password', true);

        // Generate and send verification code
        $code = $this->verificationCodeModel->generateCode($user['id'], $email, 10);
        
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
            'temp_user_id' => $user['id'],
            'temp_email' => $email,
            'temp_full_name' => $user['full_name'],
            'temp_role' => $user['role'],
            'awaiting_2fa' => true
        ]);

        // Log activity
        $this->activityLogModel->logActivity($user['id'], 'password_verified', 'Password verified, awaiting 2FA');

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
            'user_id' => $user['id'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'role' => $user['role'],
            'access_level' => $user['access_level'],
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

        if ($role === 'super_admin') {
            return redirect()->to('/super-admin/dashboard');
        } else {
            return redirect()->to('/dashboard');
        }
    }
}