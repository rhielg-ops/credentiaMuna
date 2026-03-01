<?php

namespace App\Models;

use CodeIgniter\Model;

class UserFolderAccessModel extends Model
{
    protected $table      = 'user_folder_access';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'folder_path', 'created_by'];

    /**
     * Get all allowed folder paths for a user as a plain string array.
     * e.g. ['2021', '2022/SEM1']
     */
    public function getAllowedFolders(int $userId): array
    {
        return array_column(
            $this->where('user_id', $userId)->findAll(),
            'folder_path'
        );
    }

    /**
     * Returns true if $relativePath is inside any of the user's allowed folders.
     * Call this only for non-admins — admins bypass entirely in the controller.
     *
     * Examples (allowed = ['2021']):
     *   '2021'          → true  (exact match — viewing the folder itself)
     *   '2021/SEM1'     → true  (subfolder)
     *   '2021/file.pdf' → true  (file inside)
     *   '2022'          → false (not assigned)
     *   ''              → false (root — not allowed unless explicitly assigned)
     */
    public function canAccessPath(int $userId, string $relativePath): bool
    {
        $allowed = $this->getAllowedFolders($userId);
        if (empty($allowed)) return false;

        $relativePath = ltrim($relativePath, '/');

        foreach ($allowed as $folder) {
            $folder = ltrim($folder, '/');
            if ($relativePath === $folder
                || str_starts_with($relativePath, $folder . '/')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Replace ALL folder assignments for a user in one call.
     * Pass an empty array to remove all access.
     */
    public function setFolders(int $userId, array $folders, int $createdBy): void
    {
        // Delete existing assignments first
        $this->where('user_id', $userId)->delete();

        foreach ($folders as $folder) {
            $folder = trim($folder, '/');
            if ($folder === '') continue;
            $this->insert([
                'user_id'     => $userId,
                'folder_path' => $folder,
                'created_by'  => $createdBy,
            ]);
        }
    }
}
