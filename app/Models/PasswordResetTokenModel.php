<?php
namespace App\Models;

use CodeIgniter\Model;

class PasswordResetTokenModel extends Model
{
    protected $table      = 'password_reset_tokens';
    protected $primaryKey = 'token_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'email', 'token', 'purpose', 'expires_at', 'is_used', 'used_at',
    ];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    /**
     * Generate a secure OTP token, invalidate old ones for this email+purpose,
     * and return the plain 6-digit OTP.
     */
    public function generateToken(int $userId, string $email, string $purpose = 'password_reset'): string
    {
        // Invalidate existing unused tokens
        $this->where('email', $email)
             ->where('purpose', $purpose)
             ->where('is_used', 0)
             ->set('is_used', 1)
             ->update();

        $otp     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $this->insert([
            'user_id'    => $userId,
            'email'      => $email,
            'token'      => $otp,
            'purpose'    => $purpose,
            'expires_at' => $expires,
            'is_used'    => 0,
        ]);

        return $otp;
    }

    /**
     * Verify a token. Returns the token row on success, null on failure.
     */
    public function verifyToken(string $email, string $otp, string $purpose = 'password_reset'): ?array
    {
        $record = $this->where('email', $email)
                       ->where('token', $otp)
                       ->where('purpose', $purpose)
                       ->where('is_used', 0)
                       ->where('expires_at >', date('Y-m-d H:i:s'))
                       ->first();

        if (!$record) return null;

        // Mark as used
        $this->update($record['token_id'], [
            'is_used' => 1,
            'used_at' => date('Y-m-d H:i:s'),
        ]);

        return $record;
    }
}
