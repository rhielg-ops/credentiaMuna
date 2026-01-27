<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

echo "=== CredentiaTAU Email Configuration Test ===\n\n";

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} else {
    die("❌ ERROR: .env file not found in " . __DIR__ . "\n");
}

// Get SMTP credentials from environment
$smtpPassword = $_ENV['SMTP_PASSWORD'] ?? getenv('SMTP_PASSWORD') ?? '';
$smtpHost = $_ENV['SMTP_HOST'] ?? getenv('SMTP_HOST') ?? 'smtp.gmail.com';
$smtpUser = $_ENV['SMTP_USER'] ?? getenv('SMTP_USER') ?? '';
$smtpPort = $_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT') ?? 587;

// Trim any whitespace
$smtpPassword = trim($smtpPassword);
$smtpHost = trim($smtpHost);
$smtpUser = trim($smtpUser);
$smtpPort = trim($smtpPort);

echo "📋 Configuration Check:\n";
echo "----------------------\n";
echo "SMTP Host: " . $smtpHost . "\n";
echo "SMTP Port: " . $smtpPort . "\n";
echo "SMTP User: " . $smtpUser . "\n";
echo "Password Length: " . strlen($smtpPassword) . " chars\n";
echo "Password (first 4): " . substr($smtpPassword, 0, 4) . "************\n";
echo "Password has spaces: " . (strpos($smtpPassword, ' ') !== false ? 'YES ❌' : 'NO ✅') . "\n\n";

if (empty($smtpPassword)) {
    die("❌ ERROR: SMTP_PASSWORD not found or empty!\n\nCheck your .env file:\n- Remove spaces around = sign\n- Example: SMTP_PASSWORD=yourpassword\n");
}

if (empty($smtpUser)) {
    die("❌ ERROR: SMTP_USER not found or empty!\n");
}

if (strlen($smtpPassword) != 16) {
    echo "⚠️  WARNING: Gmail App Passwords are 16 characters.\n";
    echo "   Yours is " . strlen($smtpPassword) . " characters.\n";
    echo "   Double-check you removed all spaces!\n\n";
}

$mail = new PHPMailer(true);

try {
    // Enable verbose debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = function($str, $level) {
        echo "[DEBUG] $str\n";
    };
    
    echo "🔧 Attempting SMTP Connection...\n";
    echo "--------------------------------\n";
    
    // Server settings
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)$smtpPort;
    $mail->Timeout    = 30;
    
    // Additional Gmail options
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    // Recipients
    $mail->setFrom($smtpUser, 'CredentiaTAU Test');
    $mail->addAddress($smtpUser, 'Test Recipient');
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'CredentiaTAU Email Test - ' . date('Y-m-d H:i:s');
    $mail->Body    = '<h2>✅ Email Test Successful!</h2>
                      <p>Your SMTP configuration is working correctly!</p>
                      <p><strong>Time:</strong> ' . date('Y-m-d H:i:s') . '</p>
                      <p><strong>Server:</strong> ' . $smtpHost . ':' . $smtpPort . '</p>
                      <p><strong>Your CredentiaTAU 2FA system is ready!</strong></p>';
    $mail->AltBody = 'Email test successful! Your SMTP configuration is working.';
    
    echo "\n📤 Sending test email...\n";
    echo "--------------------------------\n";
    
    $mail->send();
    
    echo "\n";
    echo "════════════════════════════════════════\n";
    echo "✅ SUCCESS! Email sent successfully!\n";
    echo "════════════════════════════════════════\n\n";
    echo "📧 Check your inbox: " . $smtpUser . "\n";
    echo "📁 (Also check spam/junk/promotions folder)\n\n";
    
} catch (Exception $e) {
    echo "\n";
    echo "════════════════════════════════════════\n";
    echo "❌ FAILED! Email could not be sent.\n";
    echo "════════════════════════════════════════\n\n";
    echo "Error Message: {$mail->ErrorInfo}\n\n";
    
    if (strpos($mail->ErrorInfo, 'authenticate') !== false) {
        echo "🔧 Authentication Error - Try This:\n";
        echo "-----------------------------------\n";
        echo "1. Generate a NEW App Password:\n";
        echo "   → https://myaccount.google.com/apppasswords\n";
        echo "   → Name it: CredentiaTAU\n";
        echo "   → Copy the 16-char password\n\n";
        
        echo "2. Update your .env file (NO SPACES!):\n";
        echo "   SMTP_PASSWORD=abcdefghijklmnop\n\n";
        
        echo "3. Make sure 2-Step Verification is ON:\n";
        echo "   → https://myaccount.google.com/security\n\n";
    } else {
        echo "🔧 Troubleshooting:\n";
        echo "1. Check firewall isn't blocking port 587\n";
        echo "2. Try port 465 with SSL instead of 587 with TLS\n";
        echo "3. Verify internet connection\n";
        echo "4. Check Google Account security alerts\n";
    }
}
?>