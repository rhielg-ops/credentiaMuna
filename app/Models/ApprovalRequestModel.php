<?php

namespace App\Models;

use CodeIgniter\Model;

class ApprovalRequestModel extends Model
{
    protected $table = 'approval_requests';
    protected $primaryKey = 'approval_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id',
        'email',
        'full_name',
        'request_type',
        'status',
        'message',
        'ip_address',
        'user_agent',
        'requested_at',
        'reviewed_at',
        'reviewed_by'
    ];

    protected $useTimestamps = false;

    /**
     * Create a new approval request
     */
    public function createRequest($userId, $email, $fullName, $type = 'reactivation', $message = null)
    {
        $request = \Config\Services::request();
        
        // Check if there's already a pending request for this user
        $existing = $this->where('user_id', $userId)
                         ->where('status', 'pending')
                         ->first();
        
        if ($existing) {
            return false; // Already has pending request
        }
        
        $data = [
            'user_id' => $userId,
            'email' => $email,
            'full_name' => $fullName,
            'request_type' => $type,
            'status' => 'pending',
            'message' => $message,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString()
        ];
        
        return $this->insert($data);
    }

    /**
     * Get all pending requests
     */
    public function getPendingRequests()
    {
        return $this->select('approval_requests.*, users.role, users.status as user_status')
                    ->join('users', 'approval_requests.user_id = users.user_id', 'left')
                    ->where('approval_requests.status', 'pending')
                    ->orderBy('approval_requests.requested_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get request by user ID
     */
    public function getRequestByUserId($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'pending')
                    ->first();
    }

    /**
     * Approve a request
     */
    public function approveRequest($requestId, $reviewerId)
    {
        return $this->update($requestId, [
            'status' => 'approved',
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => $reviewerId
        ]);
    }

    /**
     * Reject a request
     */
    public function rejectRequest($requestId, $reviewerId)
    {
        return $this->update($requestId, [
            'status' => 'rejected',
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => $reviewerId
        ]);
    }

    /**
     * Get pending requests count
     */
    public function getPendingCount()
    {
        return $this->where('status', 'pending')->countAllResults();
    }

    /**
     * Get all requests for a user (history)
     */
    public function getUserRequestHistory($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('requested_at', 'DESC')
                    ->findAll();
    }

    /**
     * Clean up old rejected/approved requests
     */
    public function cleanupOldRequests($days = 30)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->whereIn('status', ['approved', 'rejected'])
                    ->where('reviewed_at <', $threshold)
                    ->delete();
    }
}