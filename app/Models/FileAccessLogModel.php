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
        'user_id', 'file_path', 'action', 'details', 'ip_address', 'user_agent',
    ];

    /**
     * Log a file access event.
     * Call this from AcademicRecords::preview(), ::download(), ::deleteFile(), etc.
     *
     * Usage:
     *   $this->fileAccessLogModel->log('/2021/file.pdf', 'download');
     */
    /**
     * Log a file or folder action.
     *
     * @param string      $filePath  Relative path (e.g. "2021/file.pdf")
     * @param string      $action    One of: view, download, preview, delete,
     *                               rename, move, upload, folder_create, folder_delete
     * @param string|null $details   Optional extra context (new path, old name, etc.)
     */
   public function log(string $filePath, string $action, ?string $details = null): bool
    {
        $request = \Config\Services::request();
        $userId  = session()->get('user_id');

        // 1. Write to file_access_logs (existing behaviour — unchanged)
        $result = (bool) $this->insert([
            'user_id'    => $userId,
            'file_path'  => $filePath,
            'action'     => $action,
            'details'    => $details,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
        ]);

        // 2. Mirror into activity_logs so it appears in the audit log UI.
        //    Build a human-readable description matching the style already
        //    used by ActivityLogModel entries (e.g. "Uploaded: foo.pdf").
        $actionLabels = [
            'view'          => 'Viewed file',
            'preview'       => 'Previewed file',
            'download'      => 'Downloaded file',
            'upload'        => 'Uploaded file',
            'delete'        => 'Deleted file',
            'rename'        => 'Renamed file',
            'move'          => 'Moved file',
            'folder_create' => 'Created folder',
            'folder_delete' => 'Deleted folder',
        ];
        $label       = $actionLabels[$action] ?? ucfirst(str_replace('_', ' ', $action));
        $description = $label . ': ' . $filePath . ($details ? ' (' . $details . ')' : '');

        try {
            $db = \Config\Database::connect();
            $db->table('activity_logs')->insert([
                'user_id'     => $userId,
                'action'      => 'file_' . $action,   // prefix so it's distinct in filters
                'description' => $description,
                'ip_address'  => $request->getIPAddress(),
                'user_agent'  => $request->getUserAgent()->getAgentString(),
                // created_at uses DEFAULT current_timestamp() — no need to pass it
            ]);
        } catch (\Throwable $e) {
            // Log but do not fail — the primary file_access_logs insert already
            // succeeded. A broken activity_log mirror should not block file ops.
            log_message('error', '[FileAccessLogModel] Failed to mirror to activity_logs: ' . $e->getMessage());
        }

        return $result;
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
