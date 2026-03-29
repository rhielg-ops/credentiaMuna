<?php

namespace App\Models;

use CodeIgniter\Model;

class MpinModel extends Model
{
    protected $table      = 'user_mpin';
    protected $primaryKey = 'mpin_id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id', 'mpin', 'set_by',
        'mpin_changed_at', 'mpin_verified_at', 'mpin_expires_at',
        'created_at',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    public function getByUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    /** Check whether an admin has set an MPIN for this user */
    public function hasMpin(int $userId): bool
    {
        return $this->where('user_id', $userId)->countAllResults() > 0;
    }

    /**
     * Admin sets or resets a user's MPIN from User Management.
     * User changes their own MPIN from Profile Settings.
     * $setBy = user_id of whoever is setting it (admin or self).
     */
public function setMpin(int $userId, string $plainMpin, int $setBy): bool
    {
        $hashed   = password_hash($plainMpin, PASSWORD_BCRYPT);
        $expires  = date('Y-m-d H:i:s', strtotime('+7 days'));
        $now      = date('Y-m-d H:i:s');
        $existing = $this->getByUser($userId);
        if ($existing) {
            return (bool) $this->update($existing['mpin_id'], [
                'mpin'            => $hashed,
                'set_by'          => $setBy,
                'mpin_changed_at' => $now,
                'mpin_expires_at' => $expires,
            ]);
        }
        return (bool) $this->insert([
            'user_id'         => $userId,
            'mpin'            => $hashed,
            'set_by'          => $setBy,
            'mpin_changed_at' => $now,
            'mpin_expires_at' => $expires,
            'created_at'      => $now,
        ]);
    }

    public function verifyMpin(int $userId, string $plainMpin): bool
    {
        $record = $this->getByUser($userId);
        if (!$record) return false;
        return password_verify($plainMpin, $record['mpin']);
    }

    /** Returns true if MPIN is past its 7-day expiry */
    public function isExpired(int $userId): bool
    {
        $record = $this->getByUser($userId);
        if (!$record || empty($record['mpin_expires_at'])) return true;
        return strtotime($record['mpin_expires_at']) < time();
    }

    /** Extends expiry by 7 more days from now */
    public function refreshExpiry(int $userId): bool
    {
        $record = $this->getByUser($userId);
        if (!$record) return false;
        return (bool) $this->update($record['mpin_id'], [
            'mpin_expires_at'  => date('Y-m-d H:i:s', strtotime('+7 days')),
            'mpin_verified_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

