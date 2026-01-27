<?php

namespace App\Models;

use CodeIgniter\Model;

class VerificationCodeModel extends Model
{
    protected $table = 'verification_codes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id',
        'email',
        'code',
        'expires_at',
        'is_used',
        'used_at'
    ];

    protected $useTimestamps = false;

    /**
     * Generate a new verification code
     */
    public function generateCode($userId, $email, $expiryMinutes = 10)
    {
        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Invalidate old codes for this user
        $this->where('email', $email)
             ->where('is_used', false)
             ->set('is_used', true)
             ->update();

        // Insert new code
        $codeData = [
            'user_id' => $userId,
            'email' => $email,
            'code' => $code,
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$expiryMinutes} minutes")),
            'is_used' => false
        ];

        if ($this->insert($codeData)) {
            return $code;
        }

        return false;
    }

    /**
     * Verify a code
     */
    public function verifyCode($email, $code)
    {
        $record = $this->where('email', $email)
                       ->where('code', $code)
                       ->where('is_used', false)
                       ->where('expires_at >', date('Y-m-d H:i:s'))
                       ->first();

        if (!$record) {
            return false;
        }

        // Mark code as used
        $this->update($record['id'], [
            'is_used' => true,
            'used_at' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    /**
     * Check if a valid code exists for email
     */
    public function hasValidCode($email)
    {
        return $this->where('email', $email)
                    ->where('is_used', false)
                    ->where('expires_at >', date('Y-m-d H:i:s'))
                    ->countAllResults() > 0;
    }

    /**
     * Get latest valid code for email (for development/testing)
     */
    public function getLatestCode($email)
    {
        return $this->where('email', $email)
                    ->where('is_used', false)
                    ->where('expires_at >', date('Y-m-d H:i:s'))
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }

    /**
     * Invalidate all codes for a user
     */
    public function invalidateAllCodes($email)
    {
        return $this->where('email', $email)
                    ->where('is_used', false)
                    ->set('is_used', true)
                    ->update();
    }

    /**
     * Clean up expired codes
     */
    public function cleanupExpiredCodes($days = 7)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('expires_at <', $threshold)->delete();
    }

    /**
     * Get code statistics for a user
     */
    public function getCodeStats($email)
    {
        return [
            'total_codes_generated' => $this->where('email', $email)->countAllResults(),
            'codes_used' => $this->where('email', $email)->where('is_used', true)->countAllResults(),
            'codes_expired' => $this->where('email', $email)
                                    ->where('is_used', false)
                                    ->where('expires_at <', date('Y-m-d H:i:s'))
                                    ->countAllResults()
        ];
    }

    /**
     * Get remaining time for latest code
     */
    public function getRemainingCodeTime($email)
    {
        $code = $this->getLatestCode($email);
        
        if (!$code) {
            return 0;
        }

        $expiryTime = strtotime($code['expires_at']);
        $currentTime = time();
        $remaining = $expiryTime - $currentTime;

        return max(0, $remaining);
    }
}