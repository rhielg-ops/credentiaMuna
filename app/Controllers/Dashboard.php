<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DashboardModel;
use App\Models\UserFolderAccessModel;

class Dashboard extends BaseController
{
    protected $dashboardModel;
    protected $userFolderAccessModel;

    public function __construct()
    {
        $this->dashboardModel        = new DashboardModel();
        $this->userFolderAccessModel = new UserFolderAccessModel();
    }

    public function index()
    {
        // Make sure user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        // Load dashboard statistics dynamically
        $stats = $this->dashboardModel->getSuperAdminStats();

        // Compute KPI counts scoped to the logged-in user's assigned folders
        $userId   = (int) session()->get('user_id');
        $basePath = WRITEPATH . '../public/uploads/academic_records';
        $kpi      = $this->countAccessibleItems($basePath, $userId);

        // Prepare data for the dashboard view
        $data = [
            'title'               => 'Dashboard - CredentiaTAU',
            'email'               => session()->get('email'),
            'role'                => session()->get('role'),
            'stats'               => $stats,
            'total_files'         => $kpi['files'],
            'total_folders'       => $kpi['folders'],
            'file_types'          => $kpi['file_types'],
            'folder_distribution' => $kpi['folder_distribution'],
            'monthly_data'        => $kpi['monthly_data'],
        ];

        return view('auth/dashboard', $data);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Scan $basePath and return counts scoped to what $userId is allowed to see.
     * Admins (role === 'admin') always get full access.
     */

    
    private function countAccessibleItems(string $basePath, int $userId): array
    {
        log_message('debug', 'Dashboard: using updated countAccessibleItems');
        $fileCount          = 0;
        $folderCount        = 0;
        $fileTypes          = [];
        $folderDistribution = [];
        $monthlyData        = array_fill(0, 12, 0);

        if (!is_dir($basePath)) {
            return [
                'files'               => 0,
                'folders'             => 0,
                'file_types'          => [],
                'folder_distribution' => [],
                'monthly_data'        => $monthlyData,
            ];
        }

        // Admins bypass folder restrictions
        $role          = session()->get('role');
        $hasFullAccess = ($role === 'admin');

        // Fetch assigned folders for non-admins via UserFolderAccessModel
        $allowedFolders = $hasFullAccess
            ? []
            : $this->userFolderAccessModel->getAllowedFolders($userId);

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                // Build a normalized relative path (forward slashes, no leading slash)
                $relativePath = ltrim(
                    str_replace('\\', '/', str_replace($basePath, '', $item->getPathname())),
                    '/'
                );

                if (!$this->pathIsAccessible($relativePath, $allowedFolders, $hasFullAccess)) {
                    continue;
                }

              if ($item->isDir()) {
                    $folderCount++;
                    // Do NOT count directories toward folder distribution —
                    // file counts are tallied in the else branch below.
                } else {
                    $ext = strtolower(pathinfo($item->getFilename(), PATHINFO_EXTENSION));

                    // Only count valid uploaded file types
                    $validExtensions = ['pdf', 'docx', 'doc', 'png', 'jpg', 'jpeg'];
                    if (!in_array($ext, $validExtensions, true)) {
                        continue;
                    }

                    $fileCount++;

                    // Track file types
                    $fileTypes[$ext] = ($fileTypes[$ext] ?? 0) + 1;

                    // Track top-level folder distribution:
                    // Attribute every valid file to its top-level folder,
                    // regardless of how deeply nested it is.
                    // Only actual files (valid extensions) reach this point —
                    // the isDir() branch above ensures directories are never counted here.
                    $pathParts = explode('/', $relativePath);
                    if (count($pathParts) >= 2) {
                        $topFolder = $pathParts[0];
                        $folderDistribution[$topFolder] = ($folderDistribution[$topFolder] ?? 0) + 1;
                    }


                    // Track monthly uploads (current year only)
                    $mtime = filemtime($item->getPathname());
                    if ((int) date('Y', $mtime) === (int) date('Y')) {
                        $monthlyData[(int) date('n', $mtime) - 1]++;
                    }

                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Dashboard KPI scan error: ' . $e->getMessage());
        }

        return [
            'files'               => $fileCount,
            'folders'             => $folderCount,
            'file_types'          => $fileTypes,
            'folder_distribution' => $folderDistribution,
            'monthly_data'        => $monthlyData,
        ];
    }

    /**
     * Returns true if $relativePath falls within any of the $allowedFolders,
     * or if $hasFullAccess is true.
     */
    private function pathIsAccessible(string $path, array $allowedFolders, bool $hasFullAccess): bool
    {
        if ($hasFullAccess) {
            return true;
        }

        foreach ($allowedFolders as $folder) {
            $folder = ltrim($folder, '/');
            if ($path === $folder || str_starts_with($path, $folder . '/')) {
                return true;
            }
        }

        return false;
    }
}