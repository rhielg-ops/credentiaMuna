<?php

namespace App\Libraries;

class EmailService
{
    protected $email;

    public function __construct()
    {
        $this->email = \Config\Services::email();
    }

    /**
     * Send approval request notification to Super Admin
     */
    public function sendApprovalRequestNotification($adminEmail, $adminName, $userName, $userEmail)
    {
        $this->email->setFrom('artryry6@gmail.com', 'CredentiaTAU System');
        $this->email->setTo($adminEmail);
        $this->email->setSubject('New Account Reactivation Request - CredentiaTAU');

        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1d6b37; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
                .button { display: inline-block; padding: 12px 30px; background: #1d6b37; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
                .info { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🔔 New Reactivation Request</h2>
                </div>
                <div class='content'>
                    <p>Hello <strong>{$adminName}</strong>,</p>
                    
                    <p>A user has requested account reactivation in CredentiaTAU:</p>
                    
                    <div class='info'>
                        <strong>User Details:</strong><br>
                        Name: {$userName}<br>
                        Email: {$userEmail}<br>
                        Request Date: " . date('F j, Y g:i A') . "
                    </div>
                    
                    <p>Please log in to the Super Admin dashboard to review and approve/reject this request.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . base_url('super-admin/user-management') . "' class='button'>Review Request</a>
                    </div>
                    
                    <p style='color: #666; font-size: 14px; margin-top: 30px;'>
                        This is an automated message from CredentiaTAU System.<br>
                        Please do not reply to this email.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";

        $this->email->setMessage($message);
        
        try {
            return $this->email->send();
        } catch (\Exception $e) {
            log_message('error', 'Email send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send approval/rejection notification to user
     */
    public function sendApprovalNotification($userEmail, $userName, $isApproved, $message = '')
    {
        $this->email->setFrom('artryry6@gmail.com', 'CredentiaTAU System');
        $this->email->setTo($userEmail);
        
        if ($isApproved) {
            $this->email->setSubject('Account Reactivation Approved - CredentiaTAU');
            $status = 'Approved';
            $statusColor = '#28a745';
            $icon = '✅';
            $actionMessage = 'Your account has been successfully reactivated. You can now log in to CredentiaTAU.';
        } else {
            $this->email->setSubject('Account Reactivation Request Reviewed - CredentiaTAU');
            $status = 'Not Approved';
            $statusColor = '#dc3545';
            $icon = '❌';
            $actionMessage = 'Your account reactivation request has been reviewed. Please contact your administrator for more information.';
        }

        $emailMessage = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: {$statusColor}; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
                .button { display: inline-block; padding: 12px 30px; background: #1d6b37; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
                .status { background: white; padding: 20px; border-left: 4px solid {$statusColor}; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>{$icon} Request Status: {$status}</h2>
                </div>
                <div class='content'>
                    <p>Hello <strong>{$userName}</strong>,</p>
                    
                    <div class='status'>
                        <p><strong>Your account reactivation request has been reviewed.</strong></p>
                        <p>{$actionMessage}</p>
                        " . ($message ? "<p>{$message}</p>" : "") . "
                    </div>
                    
                    " . ($isApproved ? "
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . base_url('login') . "' class='button'>Log In Now</a>
                    </div>
                    " : "") . "
                    
                    <p style='color: #666; font-size: 14px; margin-top: 30px;'>
                        This is an automated message from CredentiaTAU System.<br>
                        Please do not reply to this email.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";

        $this->email->setMessage($emailMessage);
        
        try {
            return $this->email->send();
        } catch (\Exception $e) {
            log_message('error', 'Email send failed: ' . $e->getMessage());
            return false;
        }
    }

    // ... other existing methods like sendVerificationCode, sendWelcomeEmail, etc.
}