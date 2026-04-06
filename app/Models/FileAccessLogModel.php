<?php

namespace App\Models;

use CodeIgniter\Model;

class FileAccessLogModel extends Model
{
    protected $table      = 'file_access_logs';
    protected $primaryKey = 'access_id';
    protected $returnType = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'user_id', 'file_path', 'action', 'ip_address', 'user_agent',
    ];

    /**
     * Log a file access event.
     * Call this from AcademicRecords::preview(), ::download(), ::deleteFile(), etc.
     *
     * Usage:
     *   $this->fileAccessLogModel->log('/2021/file.pdf', 'download');
     */
    public function log(string $filePath, string $action): bool
    {
        $request = \Config\Services::request();
        return (bool) $this->insert([
            'user_id'    => session()->get('user_id'),
            'file_path'  => $filePath,
            'action'     => $action,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
        ]);
    }

    /**
     * Get recent access history for a specific file.
     */
    public function getFileHistory(string $filePath, int $limit = 50): array
    {
        return $this->db->table('file_access_logs fal')
            ->select('fal.*, u.full_name, u.email')
            ->join('users u', 'u.user_id = fal.user_id', 'left')
            ->where('fal.file_path', $filePath)
            ->orderBy('fal.accessed_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Get all file actions by a specific user.
     */
    public function getUserFileActivity(int $userId, int $limit = 100): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('accessed_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Summary: how many times each action was done in a date range.
     */
    public function getActionSummary(string $since = '-30 days'): array
    {
        return $this->db->table('file_access_logs')
            ->select('action, COUNT(*) as total')
            ->where('accessed_at >=', date('Y-m-d H:i:s', strtotime($since)))
            ->groupBy('action')
            ->get()->getResultArray();
    }
}
