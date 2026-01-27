<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginAttemptModel extends Model
{
    protected $table = 'login_attempts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'email',
        'attempt_type',
        'is_successful',
        'ip_address',
        'user_agent',
        'attempted_at'
    ];

    protected $useTimestamps = false;

    /**
     * Record a login attempt
     */
    public function recordAttempt($email, $type, $isSuccessful = false)
    {
        $request = \Config\Services::request();
        
        return $this->insert([
            'email' => $email,
            'attempt_type' => $type,
            'is_successful' => $isSuccessful,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString()
        ]);
    }

    /**
     * Get failed attempts count within time window
     */
    public function getFailedAttemptsCount($email, $type, $minutes = 30)
    {
        $timeThreshold = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        
        return $this->where('email', $email)
                    ->where('attempt_type', $type)
                    ->where('is_successful', false)
                    ->where('attempted_at >=', $timeThreshold)
                    ->countAllResults();
    }

    /**
     * Get recent failed attempts
     */
    public function getRecentFailedAttempts($email, $type, $limit = 5)
    {
        $timeThreshold = date('Y-m-d H:i:s', strtotime('-30 minutes'));
        
        return $this->where('email', $email)
                    ->where('attempt_type', $type)
                    ->where('is_successful', false)
                    ->where('attempted_at >=', $timeThreshold)
                    ->orderBy('attempted_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Clear old attempts (for maintenance)
     */
    public function clearOldAttempts($days = 30)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('attempted_at <', $threshold)->delete();
    }

    /**
     * Get attempt statistics for a user
     */
    public function getUserAttemptStats($email)
    {
        return [
            'total_password_attempts' => $this->where('email', $email)
                                              ->where('attempt_type', 'password')
                                              ->countAllResults(),
            'failed_password_attempts' => $this->where('email', $email)
                                               ->where('attempt_type', 'password')
                                               ->where('is_successful', false)
                                               ->countAllResults(),
            'total_code_attempts' => $this->where('email', $email)
                                          ->where('attempt_type', 'verification_code')
                                          ->countAllResults(),
            'failed_code_attempts' => $this->where('email', $email)
                                           ->where('attempt_type', 'verification_code')
                                           ->where('is_successful', false)
                                           ->countAllResults()
        ];
    }
}