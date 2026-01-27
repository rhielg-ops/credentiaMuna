<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent'
    ];

    protected $useTimestamps = false;

    /**
     * Log an activity
     */
    public function logActivity($userId, $action, $description = null)
    {
        $request = \Config\Services::request();
        
        return $this->insert([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString()
        ]);
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities($limit = 50)
    {
        return $this->select('activity_logs.*, users.full_name, users.email')
                    ->join('users', 'activity_logs.user_id = users.id', 'left')
                    ->orderBy('activity_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get activities by user
     */
    public function getUserActivities($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get activities by action
     */
    public function getActivitiesByAction($action, $limit = 50)
    {
        return $this->select('activity_logs.*, users.full_name, users.email')
                    ->join('users', 'activity_logs.user_id = users.id', 'left')
                    ->where('activity_logs.action', $action)
                    ->orderBy('activity_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get activity statistics
     */
    public function getActivityStats($days = 30)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return [
            'total_activities' => $this->where('created_at >=', $threshold)->countAllResults(),
            'login_activities' => $this->where('action', 'login')->where('created_at >=', $threshold)->countAllResults(),
            'upload_activities' => $this->like('action', 'upload')->where('created_at >=', $threshold)->countAllResults(),
            'user_management_activities' => $this->like('action', 'user_')->where('created_at >=', $threshold)->countAllResults()
        ];
    }

    /**
     * Clean up old logs
     */
    public function cleanupOldLogs($days = 90)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('created_at <', $threshold)->delete();
    }
}