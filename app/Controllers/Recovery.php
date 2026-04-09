<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PasswordResetTokenModel;
use App\Models\AccountLockModel;
use App\Models\ActivityLogModel;
use App\Models\MpinModel;
use App\Libraries\EmailService;

class Recovery extends BaseController
{
    protected UserModel             $userModel;
    protected PasswordResetTokenModel $tokenModel;
    protected AccountLockModel      $lockModel;
    protected ActivityLogModel      $activityLogModel;
    protected MpinModel             $mpinModel;
    protected EmailService          $emailService;

    public function __construct()
    {
        $this->userModel        = new UserModel();
        $this->tokenModel       = new PasswordResetTokenModel();
        $this->lockModel        = new AccountLockModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->mpinModel        = new MpinModel();
        $this->emailService     = new EmailService();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PASSWORD / USERNAME RECOVERY
    // ─────────────────────────────────────────────────────────────────────────

    public function forgotPage()
    {
        if (session()->get('logged_in')) return redirect()->to('/dashboard');
        return view('auth/forgot', []);
    }

    public function sendRecoveryOtp()
    {
        $email = trim($this->request->getPost('email') ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Please enter a valid email address.');
        }

        // Check lock (recovery_attempts)
        if ($this->lockModel->isAccountLocked($email, 'recovery_attempts')) {
            $mins = ceil($this->lockModel->getRemainingLockTime($email, 'recovery_attempts') / 60);
            return redirect()->back()->with('error', "Too many attempts. Try again in {$mins} minute(s).");
        }

        $user = $this->userModel->where('email', $email)->first();

        // Always show success message to prevent email enumeration
        if (!$user) {
            return redirect()->to('/auth/forgot-verify')
                ->with('info', 'If that email exists, an OTP has been sent.')
                ->with('recovery_email', $email);
        }

        $otp = $this->tokenModel->generateToken((int)$user['user_id'], $email, 'password_reset');
        $this->emailService->sendRecoveryOtp($email, $user['full_name'], $otp, 'Password Recovery');

        $this->activityLogModel->logActivity(
            $user['user_id'], 'recovery_otp_sent', 'Password recovery OTP sent to: ' . $email
        );

        session()->set('recovery_email', $email);
        session()->set('recovery_purpose', 'password_reset');

        return redirect()->to('/auth/forgot-verify');
    }

    public function verifyOtpPage()
    {
        $email = session()->get('recovery_email');
        if (!$email) return redirect()->to('/auth/forgot');
        return view('auth/forgot_verify', ['email' => $email, 'purpose' => 'password_reset']);
    }

    public function verifyOtp()
    {
        $email = session()->get('recovery_email');
        if (!$email) return redirect()->to('/auth/forgot');

        $otp = trim($this->request->getPost('otp') ?? '');

        // Lock check
        if ($this->lockModel->isAccountLocked($email, 'recovery_attempts')) {
            $mins = ceil($this->lockModel->getRemainingLockTime($email, 'recovery_attempts') / 60);
            return redirect()->back()->with('error', "Account locked. Try again in {$mins} minute(s).");
        }

        $record = $this->tokenModel->verifyToken($email, $otp, 'password_reset');

        if (!$record) {
            // Record failed attempt
            $this->_recordRecoveryAttempt($email);
            return redirect()->back()->with('error', 'Invalid or expired OTP. Please try again.');
        }

        // OTP verified — create temporary recovery session
        $user = $this->userModel->find($record['user_id']);
        session()->set([
            'recovery_verified'    => true,
            'recovery_user_id'     => $user['user_id'],
            'recovery_user_name'   => $user['full_name'],
            'recovery_user_email'  => $user['email'],
            'recovery_username'    => $user['username'] ?? '',
        ]);

        $this->activityLogModel->logActivity(
            $user['user_id'], 'recovery_otp_verified', 'Password recovery OTP verified'
        );

        return redirect()->to('/auth/recovery-dashboard');
    }

    public function recoveryDashboard()
    {
        if (!session()->get('recovery_verified')) return redirect()->to('/auth/forgot');
        return view('auth/recovery_dashboard', [
            'full_name' => session()->get('recovery_user_name'),
            'username'  => session()->get('recovery_username'),
            'email'     => session()->get('recovery_user_email'),
        ]);
    }

    public function resetPassword()
    {
        if (!session()->get('recovery_verified')) return redirect()->to('/auth/forgot');

        $newPassword = $this->request->getPost('new_password');
        $confirm     = $this->request->getPost('confirm_password');

        if (!$newPassword || strlen($newPassword) < 8) {
            return redirect()->back()->with('error', 'Password must be at least 8 characters.');
        }
        if ($newPassword !== $confirm) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        $userId = (int) session()->get('recovery_user_id');
        $this->userModel->update($userId, ['password' => $newPassword]);

        $this->activityLogModel->logActivity(
            $userId, 'password_reset_via_recovery', 'Password reset via email OTP recovery'
        );

        // Clear recovery session
        session()->remove(['recovery_verified','recovery_user_id','recovery_user_name',
                           'recovery_user_email','recovery_username','recovery_email','recovery_purpose']);

        return redirect()->to('/login')->with('success', 'Password reset successfully. Please log in.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FORGOT MPIN
    // ─────────────────────────────────────────────────────────────────────────

    public function forgotMpinPage()
    {
        // Must be mid-login (awaiting_mpin set) OR already logged in
        if (!session()->get('awaiting_mpin') && !session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        return view('auth/forgot_mpin', []);
    }

    public function sendMpinOtp()
    {
        $email = trim($this->request->getPost('email') ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Please enter a valid email address.');
        }

        if ($this->lockModel->isAccountLocked($email, 'recovery_attempts')) {
            $mins = ceil($this->lockModel->getRemainingLockTime($email, 'recovery_attempts') / 60);
            return redirect()->back()->with('error', "Locked. Try again in {$mins} minute(s).");
        }

        $user = $this->userModel->where('email', $email)->first();
        if (!$user) {
            // Anti-enumeration
            session()->set('mpin_recovery_email', $email);
            return redirect()->to('/auth/forgot-mpin-verify');
        }

        $otp = $this->tokenModel->generateToken((int)$user['user_id'], $email, 'mpin_reset');
        $this->emailService->sendRecoveryOtp($email, $user['full_name'], $otp, 'MPIN Recovery');

        $this->activityLogModel->logActivity(
            $user['user_id'], 'mpin_recovery_otp_sent', 'MPIN recovery OTP sent'
        );

        session()->set('mpin_recovery_email', $email);
        return redirect()->to('/auth/forgot-mpin-verify');
    }

    public function verifyMpinOtpPage()
    {
        $email = session()->get('mpin_recovery_email');
        if (!$email) return redirect()->to('/auth/forgot-mpin');
        return view('auth/forgot_verify', ['email' => $email, 'purpose' => 'mpin_reset']);
    }

    public function verifyMpinOtp()
    {
        $email = session()->get('mpin_recovery_email');
        if (!$email) return redirect()->to('/auth/forgot-mpin');

        $otp = trim($this->request->getPost('otp') ?? '');

        if ($this->lockModel->isAccountLocked($email, 'recovery_attempts')) {
            $mins = ceil($this->lockModel->getRemainingLockTime($email, 'recovery_attempts') / 60);
            return redirect()->back()->with('error', "Locked. Try again in {$mins} minute(s).");
        }

        $record = $this->tokenModel->verifyToken($email, $otp, 'mpin_reset');
        if (!$record) {
            $this->_recordRecoveryAttempt($email);
            return redirect()->back()->with('error', 'Invalid or expired OTP.');
        }

        session()->set('mpin_reset_verified', true);
        session()->set('mpin_reset_user_id',  $record['user_id']);

        $this->activityLogModel->logActivity(
            $record['user_id'], 'mpin_recovery_otp_verified', 'MPIN recovery OTP verified'
        );

        return redirect()->to('/auth/reset-mpin');
    }

    public function resetMpinPage()
    {
        if (!session()->get('mpin_reset_verified')) return redirect()->to('/auth/forgot-mpin');
        return view('auth/reset_mpin', []);
    }

    public function resetMpin()
    {
        if (!session()->get('mpin_reset_verified')) return redirect()->to('/auth/forgot-mpin');

        $newMpin    = $this->request->getPost('new_mpin');
        $confirmPin = $this->request->getPost('confirm_mpin');
        $userId     = (int) session()->get('mpin_reset_user_id');

        if (!$newMpin || strlen((string)$newMpin) !== 4 || !ctype_digit($newMpin)) {
            return redirect()->back()->with('error', 'MPIN must be exactly 4 digits.');
        }
        if ($newMpin !== $confirmPin) {
            return redirect()->back()->with('error', 'MPINs do not match.');
        }

        $this->mpinModel->setMpin($userId, $newMpin, $userId);
        $this->mpinModel->refreshExpiry($userId);

        $this->activityLogModel->logActivity(
            $userId, 'mpin_reset_via_recovery', 'MPIN reset via email OTP recovery'
        );

        // Clear MPIN recovery session keys
        session()->remove(['mpin_reset_verified','mpin_reset_user_id','mpin_recovery_email']);

        // If user was mid-login (awaiting_mpin), clear that too and send to login
        if (session()->get('awaiting_mpin')) {
            session()->remove(['awaiting_mpin','temp_user_id','temp_email',
                               'temp_full_name','temp_role','awaiting_2fa']);
        }

        return redirect()->to('/login')->with('success', 'MPIN reset successfully. Please log in.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function _recordRecoveryAttempt(string $email): void
    {
        $db = \Config\Database::connect();
        // Count recent failed attempts (last 15 min)
        $since   = date('Y-m-d H:i:s', strtotime('-15 minutes'));
        $attempts = $db->table('account_locks')
            ->where('email', $email)
            ->where('lock_type', 'recovery_attempts')
            ->where('locked_at >=', $since)
            ->countAllResults();

        // After 3 failed attempts, lock for 15 minutes
        if ($attempts >= 2) { // 0-indexed: 0,1,2 = 3 total attempts
            $this->lockModel->lockAccount($email, 'recovery_attempts', 15);
        } else {
            // Record the attempt as a minimal lock entry that expires immediately
            // (we use the locks table to count; expiry = now so it never blocks by itself)
            $db->table('account_locks')->insert([
                'email'       => $email,
                'lock_type'   => 'recovery_attempts',
                'locked_at'   => date('Y-m-d H:i:s'),
                'unlock_at'   => date('Y-m-d H:i:s', strtotime('+1 second')),
                'is_unlocked' => 0,
            ]);
        }
    }
}
