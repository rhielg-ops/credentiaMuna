<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'full_name',
        'email',
        'username',
        'password',
        'role',
        'access_level',
        'status',
        'initial_password_changed',
        'created_by',
        'last_login'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'full_name' => 'required|min_length[3]|max_length[255]',
        'email' => 'required|valid_email',
        'role' => 'required|in_list[admin,user]',
        'access_level' => 'in_list[full,limited]',
        'status' => 'in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'email' => [
            'valid_email' => 'Please enter a valid email address.'
        ]
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password before saving - FIXED VERSION
     */
    protected function hashPassword(array $data)
    {
        // Only hash if password field is present and not empty
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            // If password is empty, remove it from the update data
            unset($data['data']['password']);
        }
        return $data;
    }

    /**
     * Verify user credentials
     */
    public function verifyCredentials($email, $password)
    {
        $user = $this->where('email', $email)->first();
        
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get all active users
     */
    public function getActiveUsers()
    {
        return $this->where('status', 'active')->findAll();
    }

    /**
     * Get inactive users (for reactivation requests)
     */
    public function getInactiveUsers()
    {
        return $this->where('status', 'inactive')->findAll();
    }

    /**
     * Get user statistics
     */
    public function getUserStats()
    {
        $db = \Config\Database::connect();
        
        return [
            'total_admins' => $this->whereIn('role', 'admin')->countAllResults(),
            'active_admins' => $this->whereIn('role', 'admin')->where('status', 'active')->countAllResults(),
            'inactive_admins' => $this->where('status', 'inactive')->countAllResults(),
            'total_users' => $this->countAllResults()
        ];
    }

    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null)
    {
        $builder = $this->where('email', $email);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get user with record count
     */
    public function getUserWithRecordCount($userId)
    {
        return $this->select('users.*, COUNT(academic_records.id) as total_records')
                    ->join('academic_records', 'users.id = academic_records.uploaded_by', 'left')
                    ->where('users.id', $userId)
                    ->groupBy('users.id')
                    ->first();
    }

    /**
     * Get all users with record counts
     */
    public function getAllUsersWithRecordCounts()
    {
        return $this->select('users.*, COUNT(academic_records.id) as total_records')
                    ->join('academic_records', 'users.id = academic_records.uploaded_by', 'left')
                    ->groupBy('users.id')
                    ->orderBy('users.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Update user without password change
     */
    public function updateUser($userId, $data)
    {
        // If password is not provided or empty, remove it from update
        if (!isset($data['password']) || empty($data['password'])) {
            unset($data['password']);
        }
        
        return $this->update($userId, $data);
    }

    /**
     * Get role display name (updated naming)
     */
    public function getRoleDisplayName($role)
    {
        $roleNames = [
    'admin' => 'Admin (Former Super Admin)',
    'user' => 'User (Former Admin)'
];
        
        return $roleNames[$role] ?? ucfirst($role);
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null)
    {
        $builder = $this->where('username', $username);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
}