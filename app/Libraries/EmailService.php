<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    protected $mailer;
    protected $systemSettings;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->loadSystemSettings();
        $this->configureMailer();
    }

    /**
     * Load system settings from database
     */
    protected function loadSystemSettings()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT setting_key, setting_value FROM system_settings");
        $settings = $query->getResultArray();
        
        $this->systemSettings = [];
        foreach ($settings as $setting) {
            $this->systemSettings[$setting['setting_key']] = $setting['setting_value'];
        }
    }

    /**
     * Configure PHPMailer
     */
    protected function configureMailer()
    {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host       = $this->systemSettings['smtp_host'] ?? 'smtp.gmail.com';
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $this->systemSettings['smtp_username'] ?? 'artryry6@gmail.com';
            $this->mailer->Password   = getenv('SMTP_PASSWORD'); // From .env file
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = $this->systemSettings['smtp_port'] ?? 587;

            // Character set
            $this->mailer->CharSet = 'UTF-8';
            
            // Default sender
            $this->mailer->setFrom(
                $this->systemSettings['system_email'] ?? 'artryry6@gmail.com',
                'CredentiaTAU System'
            );
        } catch (Exception $e) {
            log_message('error', 'Email configuration error: ' . $e->getMessage());
        }
    }

    /**
     * Send verification code email
     */
    public function sendVerificationCode($to, $recipientName, $code)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to, $recipientName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'CredentiaTAU - Verification Code';
            
            $this->mailer->Body = $this->getVerificationCodeTemplate($recipientName, $code);
            $this->mailer->AltBody = "Your verification code is: $code\n\nThis code will expire in 10 minutes.";

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            log_message('error', 'Email sending failed: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Send welcome email to new admin
     */
    public function sendWelcomeEmail($to, $recipientName, $initialPassword, $createdBy)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to, $recipientName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Welcome to CredentiaTAU';
            
            $this->mailer->Body = $this->getWelcomeEmailTemplate($recipientName, $to, $initialPassword, $createdBy);
            $this->mailer->AltBody = "Welcome to CredentiaTAU!\n\nYour account has been created.\nEmail: $to\nInitial Password: $initialPassword\n\nPlease login and change your password.";

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            log_message('error', 'Welcome email failed: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Send account locked notification
     */
    public function sendAccountLockedEmail($to, $recipientName, $unlockTime)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to, $recipientName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'CredentiaTAU - Account Temporarily Locked';
            
            $this->mailer->Body = $this->getAccountLockedTemplate($recipientName, $unlockTime);
            $this->mailer->AltBody = "Your account has been temporarily locked due to multiple failed login attempts.\n\nUnlock time: $unlockTime";

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            log_message('error', 'Lock notification email failed: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Verification code email template
     */
    protected function getVerificationCodeTemplate($name, $code)
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .code-box { background: white; border: 2px dashed #16a34a; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
        .code { font-size: 32px; font-weight: bold; color: #16a34a; letter-spacing: 8px; font-family: 'Courier New', monospace; }
        .warning { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin: 20px 0; border-radius: 4px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">CredentiaTAU</h1>
            <p style="margin: 10px 0 0 0;">Two-Factor Authentication</p>
        </div>
        <div class="content">
            <p>Hello <strong>$name</strong>,</p>
            
            <p>You are attempting to log in to CredentiaTAU. Please use the verification code below to complete your login:</p>
            
            <div class="code-box">
                <div class="code">$code</div>
            </div>
            
            <div class="warning">
                <strong>⏱️ Important:</strong> This code will expire in <strong>10 minutes</strong>.
            </div>
            
            <p><strong>Security Tips:</strong></p>
            <ul>
                <li>Never share this code with anyone</li>
                <li>CredentiaTAU will never ask for your verification code</li>
                <li>If you didn't request this code, please ignore this email</li>
            </ul>
            
            <p>If you have any questions, please contact your system administrator.</p>
        </div>
        <div class="footer">
            <p>This is an automated message from CredentiaTAU Academic Records Management System</p>
            <p>&copy; 2025 Tarlac Agricultural University. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Welcome email template
     */
    protected function getWelcomeEmailTemplate($name, $email, $password, $createdBy)
    {
        $loginUrl = base_url('login');
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .credentials { background: white; border: 2px solid #16a34a; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .credential-row { display: flex; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .credential-label { font-weight: bold; width: 150px; }
        .credential-value { color: #16a34a; font-family: 'Courier New', monospace; }
        .button { display: inline-block; background: #16a34a; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .warning { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin: 20px 0; border-radius: 4px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">Welcome to CredentiaTAU</h1>
            <p style="margin: 10px 0 0 0;">Academic Records Management System</p>
        </div>
        <div class="content">
            <p>Hello <strong>$name</strong>,</p>
            
            <p>Your administrator account has been created by <strong>$createdBy</strong>. You now have access to the CredentiaTAU system.</p>
            
            <div class="credentials">
                <h3 style="margin-top: 0; color: #16a34a;">Your Login Credentials</h3>
                <div class="credential-row">
                    <div class="credential-label">Email:</div>
                    <div class="credential-value">$email</div>
                </div>
                <div class="credential-row" style="border: none;">
                    <div class="credential-label">Password:</div>
                    <div class="credential-value">$password</div>
                </div>
            </div>
            
            <div class="warning">
                <strong>🔒 Important Security Notice:</strong><br>
                You must change your password upon first login for security reasons.
            </div>
            
            <div style="text-align: center;">
                <a href="$loginUrl" class="button">Login to CredentiaTAU</a>
            </div>
            
            <p><strong>What's Next?</strong></p>
            <ol>
                <li>Click the login button above or visit: <a href="$loginUrl">$loginUrl</a></li>
                <li>Enter your email and initial password</li>
                <li>Complete the two-factor authentication</li>
                <li>Change your password when prompted</li>
            </ol>
            
            <p>If you have any questions or need assistance, please contact your system administrator.</p>
        </div>
        <div class="footer">
            <p>This is an automated message from CredentiaTAU</p>
            <p>&copy; 2025 Tarlac Agricultural University. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Account locked email template
     */
    protected function getAccountLockedTemplate($name, $unlockTime)
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .alert { background: #fee2e2; border: 2px solid #dc2626; padding: 20px; margin: 20px 0; border-radius: 8px; text-align: center; }
        .unlock-time { font-size: 24px; font-weight: bold; color: #dc2626; margin: 10px 0; }
        .info-box { background: #dbeafe; border-left: 4px solid #3b82f6; padding: 12px; margin: 20px 0; border-radius: 4px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">🔒 Security Alert</h1>
            <p style="margin: 10px 0 0 0;">Account Temporarily Locked</p>
        </div>
        <div class="content">
            <p>Hello <strong>$name</strong>,</p>
            
            <div class="alert">
                <p style="margin: 0; font-size: 18px;"><strong>Your account has been temporarily locked</strong></p>
                <p style="margin: 10px 0;">Due to multiple failed login attempts</p>
                <div class="unlock-time">Unlock at: $unlockTime</div>
            </div>
            
            <p>Your account has been automatically locked for <strong>5 minutes</strong> as a security measure after detecting multiple failed login attempts.</p>
            
            <div class="info-box">
                <strong>ℹ️ What This Means:</strong><br>
                You will not be able to log in until the unlock time shown above. This is a temporary security measure to protect your account.
            </div>
            
            <p><strong>What You Should Do:</strong></p>
            <ul>
                <li>Wait until the unlock time to try logging in again</li>
                <li>Make sure you're using the correct password</li>
                <li>If you forgot your password, contact your system administrator</li>
                <li>If you didn't attempt to login, report this to your administrator immediately</li>
            </ul>
            
            <p><strong>Need Help?</strong><br>
            If you believe this lockout was in error or if you need immediate access, please contact your system administrator.</p>
        </div>
        <div class="footer">
            <p>This is an automated security notification from CredentiaTAU</p>
            <p>&copy; 2025 Tarlac Agricultural University. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Notify admin that a deactivated user has requested reactivation
     */
    public function sendReactivationRequestEmail($adminEmail, $adminName, $userName, $userEmail)
    {
        $email = \Config\Services::email();
        $email->setTo($adminEmail);
        $email->setSubject('CredentiaTAU — User Reactivation Request');
        $email->setMessage("
            <p>Hello {$adminName},</p>
            <p><strong>{$userName}</strong> ({$userEmail}) has requested reactivation of their deactivated account.</p>
            <p>Please log in to the admin dashboard to review and approve or reject this request.</p>
            <p>— CredentiaTAU System</p>
        ");
        $email->setMailType('html');

        try {
            return $email->send();
        } catch (\Exception $e) {
            log_message('error', 'sendReactivationRequestEmail failed: ' . $e->getMessage());
            return false;
        }
    }

}