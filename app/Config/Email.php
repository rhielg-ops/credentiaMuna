<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    /**
     * @var string
     */
    public string $fromEmail = 'artryry6@gmail.com';

    /**
     * @var string
     */
    public string $fromName = 'CredentiaTAU System';

    /**
     * @var string
     */
    public string $recipients = '';

    /**
     * The "user agent"
     *
     * @var string
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     *
     * @var string
     */
    public string $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     *
     * @var string
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     *
     * @var string
     */
    public string $SMTPHost = 'smtp.gmail.com';

    /**
     * SMTP Username
     *
     * @var string
     */
    public string $SMTPUser = 'artryry6@gmail.com';

    /**
     * SMTP Password
     * IMPORTANT: Use Gmail App Password, not your regular Gmail password
     * Get it from: https://myaccount.google.com/apppasswords
     *
     * @var string
     */
    public string $SMTPPass = '';  // Will be loaded from .env

    /**
     * SMTP Port
     * 587 for TLS, 465 for SSL
     *
     * @var int
     */
    public int $SMTPPort = 587;

    /**
     * SMTP Timeout (in seconds)
     *
     * @var int
     */
    public int $SMTPTimeout = 5;

    /**
     * Enable persistent SMTP connections
     *
     * @var bool
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption.
     * 'tls' for port 587, 'ssl' for port 465
     *
     * @var string
     */
    public string $SMTPCrypto = 'tls';

    /**
     * Enable word-wrap
     *
     * @var bool
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     *
     * @var int
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     *
     * @var string
     */
    public string $mailType = 'html';  // Changed to 'html' for formatted emails

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     *
     * @var string
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     *
     * @var bool
     */
    public bool $validate = true;  // Changed to true for validation

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     *
     * @var int
     */
    public int $priority = 3;

    /**
     * Newline character. (Use "\r\n" to comply with RFC 822)
     *
     * @var string
     */
    public string $CRLF = "\r\n";

    /**
     * Newline character. (Use "\r\n" to comply with RFC 822)
     *
     * @var string
     */
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode.
     *
     * @var bool
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     *
     * @var int
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     *
     * @var bool
     */
    public bool $DSN = false;

    /**
     * Constructor - Load SMTP password from environment
     */
    public function __construct()
    {
        parent::__construct();
        
        // Load SMTP password from .env file
        $this->SMTPPass = getenv('SMTP_PASSWORD') ?: '';
        
        // You can also load other settings from .env if needed
        if (getenv('SMTP_HOST')) {
            $this->SMTPHost = getenv('SMTP_HOST');
        }
        
        if (getenv('SMTP_USER')) {
            $this->SMTPUser = getenv('SMTP_USER');
        }
        
        if (getenv('SMTP_PORT')) {
            $this->SMTPPort = (int) getenv('SMTP_PORT');
        }
    }
}