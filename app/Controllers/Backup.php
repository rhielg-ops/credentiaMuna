<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/**
 * Backup Controller
 * Handles all system backup functionality.
 * Backup ZIPs are stored in: public/uploads/backups/ (File Server 2 destination)
 * Source files are from: public/uploads/academic_records/
 * 
 * NO database interaction — all state stored in flat JSON config files.
 */
class Backup extends BaseController
{
    /** Source directory to back up */
    private string $sourceDir;

    /** Destination directory where ZIPs are saved (File Server 2) */
    private string $backupDir;

    /** JSON file that stores the schedule config */
    private string $scheduleFile;

    /** JSON file that stores backup history/log */
    private string $historyFile;

    public function __construct()
    {
        $resolved = realpath(FCPATH . 'uploads/academic_records/');
        $this->sourceDir = rtrim($resolved, '/\\') . DIRECTORY_SEPARATOR;
        $this->backupDir    = FCPATH . 'uploads/backups/';
        $this->scheduleFile = WRITEPATH . 'backup_schedule.json';
        $this->historyFile  = WRITEPATH . 'backup_history.json';

        // Ensure backup destination exists
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        // Ensure source exists
        if (!is_dir($this->sourceDir)) {
            mkdir($this->sourceDir, 0755, true);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Auth helpers
    // ─────────────────────────────────────────────────────────────

    private function isSuperAdmin(): bool
    {
        return session()->get('logged_in') &&
               session()->get('role') === 'admin' &&
               session()->get('access_level') === 'full';
    }

    private function checkAuth()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Unauthenticated.']);
        }
        return null;
    }

    private function checkSuperAuth()
    {
        if (!$this->isSuperAdmin()) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Super admin access required.']);
        }
        return null;
    }

    // ─────────────────────────────────────────────────────────────
    // Schedule helpers (flat JSON, no DB)
    // ─────────────────────────────────────────────────────────────

    private function readSchedule(): array
    {
        if (!file_exists($this->scheduleFile)) {
            return [
                'frequency' => 'daily',
                'time'      => '23:59',
                'day'       => 'sunday',   // for weekly
                'date'      => 1,           // for monthly (1st)
                'enabled'   => true,
                'updated_at' => null,
                'updated_by' => null,
            ];
        }
        $data = json_decode(file_get_contents($this->scheduleFile), true);
        return is_array($data) ? $data : [];
    }

    private function writeSchedule(array $schedule): void
    {
        file_put_contents($this->scheduleFile, json_encode($schedule, JSON_PRETTY_PRINT));
    }

    // ─────────────────────────────────────────────────────────────
    // History helpers
    // ─────────────────────────────────────────────────────────────

    private function readHistory(): array
    {
        if (!file_exists($this->historyFile)) return [];
        $data = json_decode(file_get_contents($this->historyFile), true);
        return is_array($data) ? $data : [];
    }

    private function appendHistory(array $entry): void
    {
        $history = $this->readHistory();
        array_unshift($history, $entry); // newest first
        // Keep last 50 entries
        $history = array_slice($history, 0, 50);
        file_put_contents($this->historyFile, json_encode($history, JSON_PRETTY_PRINT));
    }

    // ─────────────────────────────────────────────────────────────
    // Core: Create ZIP backup
    // ─────────────────────────────────────────────────────────────
    

    private function createZip(bool $overwrite = false): array
    {
        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'message' => 'ZipArchive extension not available on this server.'];
        }

        if (!is_dir($this->sourceDir)) {
            return ['success' => false, 'message' => 'Source directory does not exist.'];
        }

        // Generate filename with timestamp
        $timestamp = (new \DateTime('now', new \DateTimeZone('Asia/Manila')))->format('Y-m-d_H-i-s');
        $filename  = 'academic_records_backup_' . $timestamp . '.zip';
        $zipPath   = $this->backupDir . $filename;

        // Safety: never overwrite unless explicitly told to
        if (file_exists($zipPath) && !$overwrite) {
            // Add a counter suffix
            $i = 1;
            while (file_exists($zipPath)) {
                $filename = 'academic_records_backup_' . $timestamp . '_' . $i . '.zip';
                $zipPath  = $this->backupDir . $filename;
                $i++;
            }
        }

        $zip = new \ZipArchive();
        $result = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        if ($result !== true) {
            return ['success' => false, 'message' => 'Failed to create ZIP file. Error code: ' . $result];
        }

        // Recursively add files
        $fileCount = 0;
        $totalSize = 0;
        $errors    = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $realPath = $file->getRealPath();
            $relative = substr($realPath, strlen($this->sourceDir));

            if ($file->isDir()) {
                $zip->addEmptyDir($relative);
            } else {
                if ($zip->addFile($realPath, $relative)) {
                    $fileCount++;
                    $totalSize += $file->getSize();
                } else {
                    $errors[] = $relative;
                }
            }
        }

        $zip->close();

        if (!file_exists($zipPath)) {
            return ['success' => false, 'message' => 'ZIP file was not created.'];
        }

        $zipSize = filesize($zipPath);

        $entry = [
            'filename'   => $filename,
            'created_at' => (new \DateTime('now', new \DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s'),
            'file_count' => $fileCount,
            'source_size'=> $this->formatBytes($totalSize),
            'zip_size'   => $this->formatBytes($zipSize),
            'type'       => 'manual',
            'user'       => session()->get('full_name') ?? session()->get('email') ?? 'System',
            'errors'     => $errors,
        ];

        $this->appendHistory($entry);

        return [
            'success'    => true,
            'filename'   => $filename,
            'file_count' => $fileCount,
            'zip_size'   => $this->formatBytes($zipSize),
            'created_at' => $entry['created_at'],
            'errors'     => $errors,
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)   return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)      return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    // ─────────────────────────────────────────────────────────────
    // API: Perform manual backup (AJAX POST)
    // Route: POST super-admin/backup/run
    // ─────────────────────────────────────────────────────────────

    public function run()
    {
        if ($r = $this->checkAuth()) return $r;

        $result = $this->createZip(false);

        return $this->response->setJSON($result);
    }

    // ─────────────────────────────────────────────────────────────
    // API: Download latest or specific backup (GET)
    // Route: GET super-admin/backup/download?file=filename.zip
    // ─────────────────────────────────────────────────────────────

    public function download()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        $filename = $this->request->getGet('file');

        if (!$filename) {
            // Download the latest backup
            $files = glob($this->backupDir . '*.zip');
            if (empty($files)) {
                return $this->response->setStatusCode(404)->setBody('No backup files found.');
            }
            usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
            $filepath = $files[0];
            $filename = basename($filepath);
        } else {
            // Sanitise filename — no path traversal
            $filename = basename($filename);
            if (pathinfo($filename, PATHINFO_EXTENSION) !== 'zip') {
                return $this->response->setStatusCode(400)->setBody('Invalid file.');
            }
            $filepath = $this->backupDir . $filename;
        }

        if (!file_exists($filepath)) {
            return $this->response->setStatusCode(404)->setBody('Backup file not found.');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/zip')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Content-Length', (string) filesize($filepath))
            ->setHeader('Cache-Control', 'no-cache, no-store')
            ->setBody(file_get_contents($filepath));
    }

    // ─────────────────────────────────────────────────────────────
    // API: List backup files (AJAX GET)
    // Route: GET super-admin/backup/list
    // ─────────────────────────────────────────────────────────────

    public function listBackups()
    {
        if ($r = $this->checkAuth()) return $r;

        $files   = glob($this->backupDir . '*.zip') ?: [];
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        $list = [];
        foreach ($files as $path) {
            $list[] = [
                'filename'   => basename($path),
                'size'       => $this->formatBytes(filesize($path)),
                'created_at' => (new \DateTime('@' . filemtime($path)))->setTimezone(new \DateTimeZone('Asia/Manila'))->format('M d, Y g:i A'),
                'timestamp'  => filemtime($path),
            ];
        }

        // Count source files
        $fileCount = 0;
        if (is_dir($this->sourceDir)) {
            $it = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($it as $f) {
                if ($f->isFile()) $fileCount++;
            }
        }

        // Last backup info
        $lastBackup = null;
        if (!empty($list)) {
            $lastBackup = [
                'date' => $list[0]['created_at'],
                'file' => $list[0]['filename'],
                'size' => $list[0]['size'],
            ];
        }

        return $this->response->setJSON([
            'success'      => true,
            'backups'      => $list,
            'total'        => count($list),
            'source_count' => $fileCount,
            'last_backup'  => $lastBackup,
            'schedule'     => $this->readSchedule(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // API: Get / Save schedule (AJAX GET/POST) — Super Admin only
    // Route: GET  super-admin/backup/schedule
    //        POST super-admin/backup/schedule
    // ─────────────────────────────────────────────────────────────

    public function schedule()
    {
        if ($r = $this->checkAuth()) return $r;

        if ($this->request->getMethod() === 'get') {
            return $this->response->setJSON([
                'success'  => true,
                'schedule' => $this->readSchedule(),
            ]);
        }

        // POST — only super admin can update
        if ($r = $this->checkSuperAuth()) return $r;

        $body = $this->request->getJSON(true);

        $allowed_freqs = ['daily', 'weekly', 'monthly', 'disabled'];
        $frequency = $body['frequency'] ?? 'daily';
        if (!in_array($frequency, $allowed_freqs)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid frequency.']);
        }

        $time = $body['time'] ?? '23:59';
        // Validate HH:MM
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            $time = '23:59';
        }

        $current = $this->readSchedule();
        $schedule = array_merge($current, [
            'frequency'  => $frequency,
            'time'       => $time,
            'day'        => $body['day']  ?? 'sunday',
            'date'       => (int)($body['date'] ?? 1),
            'enabled'    => $frequency !== 'disabled',
            'updated_at' => (new \DateTime('now', new \DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s'),
            'updated_by' => session()->get('full_name') ?? session()->get('email'),
        ]);

        $this->writeSchedule($schedule);

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Schedule saved successfully.',
            'schedule' => $schedule,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // API: Delete a specific backup (AJAX POST) — Super Admin only
    // Route: POST super-admin/backup/delete
    // ─────────────────────────────────────────────────────────────

    public function deleteBackup()
    {
        if ($r = $this->checkSuperAuth()) return $r;

        $body     = $this->request->getJSON(true);
        $filename = basename($body['filename'] ?? '');

        if (!$filename || pathinfo($filename, PATHINFO_EXTENSION) !== 'zip') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid filename.']);
        }

        $path = $this->backupDir . $filename;
        if (!file_exists($path)) {
            return $this->response->setJSON(['success' => false, 'message' => 'File not found.']);
        }

        if (unlink($path)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Backup deleted.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete backup.']);
    }

    // ─────────────────────────────────────────────────────────────
    // Scheduled auto-backup endpoint (called by cron / scheduler)
    // Route: GET super-admin/backup/cron?secret=YOUR_SECRET
    // Set up a cron job: * * * * * curl http://yoursite/super-admin/backup/cron?secret=SECRET
    // ─────────────────────────────────────────────────────────────

    public function cron()
    {
        // Simple secret-key guard for cron endpoint
        $secret = $this->request->getGet('secret');
        $expected = env('BACKUP_CRON_SECRET', 'credentia_backup_secret_2025');

        if ($secret !== $expected) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $schedule = $this->readSchedule();

        if (!($schedule['enabled'] ?? false) || ($schedule['frequency'] === 'disabled')) {
            return $this->response->setBody('Backup disabled.');
        }

        $now = new \DateTime('now', new \DateTimeZone('Asia/Manila'));
        $schedTime = $schedule['time'] ?? '23:59';
        [$sHour, $sMin] = explode(':', $schedTime);

        $currentHour = (int)$now->format('H');
        $currentMin  = (int)$now->format('i');

        // Only run within the scheduled minute window
        if ($currentHour !== (int)$sHour || $currentMin !== (int)$sMin) {
            return $this->response->setBody('Not scheduled time.');
        }

        $freq = $schedule['frequency'];

        $shouldRun = false;
        if ($freq === 'daily') {
            $shouldRun = true;
        } elseif ($freq === 'weekly') {
            $dayOfWeek = strtolower($now->format('l'));
            $shouldRun = ($dayOfWeek === ($schedule['day'] ?? 'sunday'));
        } elseif ($freq === 'monthly') {
            $shouldRun = ((int)$now->format('j') === (int)($schedule['date'] ?? 1));
        }

        if (!$shouldRun) {
            return $this->response->setBody('Not the scheduled day/date.');
        }

        $result = $this->createZip(false);
        $result['type'] = 'auto';

        return $this->response->setJSON($result);
    }
}