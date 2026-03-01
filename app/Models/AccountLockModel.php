<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountLockModel extends Model
{
    protected $table = 'account_locks';
    protected $primaryKey = 'lock_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'email',
        'lock_type',
        'locked_at',
        'unlock_at',
        'is_unlocked'
    ];

    protected $useTimestamps = false;

    /**
     * Create account lock
     */
    public function lockAccount($email, $type, $durationMinutes = 5)
    {
        $lockData = [
            'email' => $email,
            'lock_type' => $type,
            'locked_at' => date('Y-m-d H:i:s'),
            'unlock_at' => date('Y-m-d H:i:s', strtotime("+{$durationMinutes} minutes")),
            'is_unlocked' => false
        ];

        return $this->insert($lockData);
    }

    /**
     * Check if account is currently locked
     */
    public function isAccountLocked($email, $type = null)
    {
        $builder = $this->where('email', $email)
                        ->where('is_unlocked', false)
                        ->where('unlock_at >', date('Y-m-d H:i:s'));
        
        if ($type) {
            $builder->where('lock_type', $type);
        }

        $lock = $builder->orderBy('unlock_at', 'DESC')->first();

        return $lock !== null;
    }

    /**
     * Get active lock information
     */
    public function getActiveLock($email, $type = null)
    {
        $builder = $this->where('email', $email)
                        ->where('is_unlocked', false)
                        ->where('unlock_at >', date('Y-m-d H:i:s'));
        
        if ($type) {
            $builder->where('lock_type', $type);
        }

        return $builder->orderBy('unlock_at', 'DESC')->first();
    }

    /**
     * Get remaining lock time in seconds
     */
    public function getRemainingLockTime($email, $type = null)
    {
        $lock = $this->getActiveLock($email, $type);
        
        if (!$lock) {
            return 0;
        }

        $unlockTime = strtotime($lock['unlock_at']);
        $currentTime = time();
        $remaining = $unlockTime - $currentTime;

        return max(0, $remaining);
    }

    /**
     * Unlock account manually
     */
    public function unlockAccount($email, $type = null)
    {
        $builder = $this->where('email', $email)
                        ->where('is_unlocked', false);
        
        if ($type) {
            $builder->where('lock_type', $type);
        }

        return $builder->set('is_unlocked', true)->update();
    }

    /**
     * Auto-unlock expired locks (maintenance)
     */
    public function unlockExpiredLocks()
    {
        return $this->where('is_unlocked', false)
                    ->where('unlock_at <=', date('Y-m-d H:i:s'))
                    ->set('is_unlocked', true)
                    ->update();
    }

    /**
     * Clean up old lock records
     */
    public function cleanupOldLocks($days = 30)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('locked_at <', $threshold)->delete();
    }

    /**
     * Get lock history for a user
     */
    public function getLockHistory($email, $limit = 10)
    {
        return $this->where('email', $email)
                    ->orderBy('locked_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}