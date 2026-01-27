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
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'role' => 'required|in_list[super_admin,admin]',
        'access_level' => 'in_list[full,limited]',
        'status' => 'in_list[active,inactive,pending,suspended]'
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email is already registered.',
            'valid_email' => 'Please enter a valid email address.'
        ],
        'password' => [
            'min_length' => 'Password must be at least 8 characters long.'
        ]
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password before saving
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
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
     * Get pending admin requests
     */
    public function getPendingAdmins()
    {
        return $this->select('users.*, creator.full_name as created_by_name')
                    ->join('users as creator', 'users.created_by = creator.id', 'left')
                    ->where('users.status', 'pending')
                    ->orderBy('users.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get user statistics
     */
    public function getUserStats()
    {
        $db = \Config\Database::connect();
        
        return [
            'total_admins' => $this->whereIn('role', ['admin', 'super_admin'])->countAllResults(),
            'active_admins' => $this->whereIn('role', ['admin', 'super_admin'])->where('status', 'active')->countAllResults(),
            'pending_admins' => $this->where('status', 'pending')->countAllResults(),
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
}