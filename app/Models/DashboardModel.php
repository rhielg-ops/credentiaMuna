<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get Super Admin Dashboard Statistics
     */
    public function getSuperAdminStats()
    {
        return [
            'total_records' => $this->getTotalRecords(),
            'total_admins' => $this->getTotalAdmins(),
            'active_admins' => $this->getActiveAdmins(),
            'recent_activity' => $this->getRecentActivityCount(),
            'pending_staff' => $this->getPendingStaffCount()
        ];
    }

    /**
     * Get total records count
     */
   protected function getTotalRecords()
    {
        $uploadRoot = FCPATH . 'uploads/academic_records/';

        if (!is_dir($uploadRoot)) {
            return 0;
        }

        $total = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($uploadRoot, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            // Count both files and folders (excluding . and ..)
            $total++;
        }

        return $total;
    }

    /**
     * Get total admins count
     */
    protected function getTotalAdmins()
    {
        $query = $this->db->query("SELECT COUNT(*) as total FROM users");
        $result = $query->getRow();
        return $result ? $result->total : 0;
    }

    /**
     * Get active admins count
     */
    protected function getActiveAdmins()
    {
        $query = $this->db->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
        $result = $query->getRow();
        return $result ? $result->total : 0;
    }

    /**
     * Get recent activity count (today)
     */
    protected function getRecentActivityCount()
    {
        $today = date('Y-m-d');
        $query = $this->db->query("SELECT COUNT(*) as total FROM activity_logs WHERE DATE(created_at) = ?", [$today]);
        $result = $query->getRow();
        return $result ? $result->total : 0;
    }

    /**
     * Get pending staff requests count
     */
    protected function getPendingStaffCount()
    {
        $query = $this->db->query("SELECT COUNT(*) as total FROM approval_requests WHERE status = 'pending'");
        $result = $query->getRow();
        return $result ? $result->total : 0;
    }

    /**
     * Get recent activity log entries
     */
    public function getRecentActivity($limit = 5)
    {
        $query = $this->db->query("
            SELECT 
                al.*, 
                u.full_name, 
                u.email,
                u.role
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC
            LIMIT ?
        ", [$limit]);

        $activities = $query->getResultArray();
        
        // Format activities with icons and colors
        $formatted = [];
        foreach ($activities as $activity) {
            $iconBg = 'blue';
            
            // Determine icon background based on action type
            if (strpos($activity['action'], 'delete') !== false) {
                $iconBg = 'red';
            } elseif (strpos($activity['action'], 'create') !== false || strpos($activity['action'], 'add') !== false) {
                $iconBg = 'green';
            } elseif (strpos($activity['action'], 'update') !== false || strpos($activity['action'], 'edit') !== false) {
                $iconBg = 'yellow';
            }
            
            $formatted[] = [
                'title' => $this->formatActivityTitle($activity['action'], $activity['description']),
                'user' => $activity['full_name'] ?? 'System',
                'time' => $this->timeAgo($activity['created_at']),
                'timestamp' => date('M d, Y H:i', strtotime($activity['created_at'])),
                'icon_bg' => $iconBg
            ];
        }
        
        return $formatted;
    }

    /**
     * Format activity title
     */
    protected function formatActivityTitle($action, $description)
    {
        $titles = [
            'login_success' => 'Logged In',
            'logout' => 'Logged Out',
            'user_created' => 'New User Created',
            'user_updated' => 'User Updated',
            'user_deleted' => 'User Deleted',
            'record_uploaded' => 'Record Uploaded',
            'record_updated' => 'Record Updated',
            'record_deleted' => 'Record Deleted',
            'profile_updated' => 'Profile Updated',
            'password_changed' => 'Password Changed'
        ];
        
        if (isset($titles[$action])) {
            return $titles[$action];
        }
        
        // Use description or format action
        return $description ?: ucwords(str_replace('_', ' ', $action));
    }

    /**
     * Convert timestamp to "time ago" format
     */
    protected function timeAgo($timestamp)
    {
        $time = strtotime($timestamp);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M d, Y', $time);
        }
    }
}