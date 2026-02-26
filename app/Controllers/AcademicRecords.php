<?php

namespace App\Controllers;

use App\Models\UserPrivilegeModel;

class AcademicRecords extends BaseController
{
    private string $uploadRoot;
    private array  $allowedExts = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    private int    $maxBytes    = 10 * 1024 * 1024;

    protected UserPrivilegeModel $privilegeModel;

    public function __construct()
    {
        $this->uploadRoot     = FCPATH . 'uploads/academic_records/';
        $this->privilegeModel = new UserPrivilegeModel();

        if (!is_dir($this->uploadRoot)) {
            mkdir($this->uploadRoot, 0755, true);
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function privs(): array
    {
        return $this->privilegeModel->getUserPrivileges(session()->get('user_id')) ?? [];
    }

    private function safePath(string $relative): string
    {
        $relative  = ltrim($relative, '/\\');
        $candidate = $this->uploadRoot . $relative;
        $root      = rtrim(realpath($this->uploadRoot), DIRECTORY_SEPARATOR);
        $real      = realpath($candidate);

        if ($real !== false) {
            if (strpos($real, $root) !== 0) throw new \RuntimeException('Path traversal detected.');
            return $real;
        }

        if (strpos($candidate, '..') !== false) throw new \RuntimeException('Path traversal detected.');
        return $candidate;
    }

    private function sanitiseName(string $name): string
    {
        return trim(preg_replace('/[^\w\s\-.]/', '', $name)) ?: 'untitled';
    }

    private function jsonError(string $msg, int $code = 400)
    {
        return $this->response->setStatusCode($code)->setJSON(['success' => false, 'message' => $msg]);
    }

    private function jsonOk(array $extra = [])
    {
        return $this->response->setJSON(array_merge(['success' => true], $extra));
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    private function scanFolders(string $relative): array
    {
        $abs = $this->uploadRoot . ltrim($relative, '/');
        if (!is_dir($abs)) return [];
        $items = [];
        foreach (scandir($abs) as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $full = $abs . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($full)) {
                $count = count(array_filter(scandir($full), fn($f) => $f !== '.' && $f !== '..' && is_file($full . DIRECTORY_SEPARATOR . $f)));
                $items[] = ['name' => $entry, 'path' => trim($relative . '/' . $entry, '/'), 'modified' => date('Y-m-d', filemtime($full)), 'count' => $count];
            }
        }
        usort($items, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        return $items;
    }

    private function scanFiles(string $relative): array
    {
        $abs = $this->uploadRoot . ltrim($relative, '/');
        if (!is_dir($abs)) return [];
        $items = [];
        foreach (scandir($abs) as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $full = $abs . DIRECTORY_SEPARATOR . $entry;
            if (is_file($full)) {
                $ext     = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                $relPath = trim($relative . '/' . $entry, '/');
                $items[] = [
                    'name'         => $entry,
                    'path'         => $relPath,
                    'size'         => $this->formatBytes(filesize($full)),
                    'ext'          => $ext,
                    'modified'     => date('Y-m-d', filemtime($full)),
                    'download_url' => base_url('academic-records/download?path=' . urlencode($relPath)),
                    'preview_url'  => base_url('academic-records/preview?path='  . urlencode($relPath)),
                ];
            }
        }
        usort($items, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        return $items;
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────

    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to('/login')->with('error', 'Please log in first.');

        $privs = $this->privs();

        return view('auth/academic_records', [
            'email'    => session()->get('email'),
            'role'     => session()->get('role'),
            'fullName' => session()->get('full_name') ?? '',

            'folders'   => $this->scanFolders(''),
            'rootFiles' => $this->scanFiles(''),

            // Academic records privileges
            'priv_records_upload'   => (bool) ($privs['records_upload']   ?? false),
            'priv_files_view'       => (bool) ($privs['files_view']       ?? false),
            'priv_records_update'   => (bool) ($privs['records_update']   ?? false),
            'priv_records_organize' => (bool) ($privs['records_organize'] ?? false),
            'priv_folders_add'      => (bool) ($privs['folders_add']      ?? false),
            'priv_records_delete'   => (bool) ($privs['records_delete']   ?? false),
            'priv_folders_delete'   => (bool) ($privs['folders_delete']   ?? false),

            // Navigation / page-access privileges (used by sidebar)
            'priv_profile_edit'    => (bool) ($privs['profile_edit']    ?? false),
            'priv_user_management' => (bool) ($privs['user_management'] ?? false),
            'priv_system_backup'   => (bool) ($privs['system_backup']   ?? false),
            'priv_audit_logs'      => (bool) ($privs['audit_logs']      ?? false),
        ]);
    }

    // ── LIST FOLDER (AJAX) ────────────────────────────────────────────────────

    public function listFolder()
    {
        if (!session()->get('logged_in')) return $this->jsonError('Unauthenticated.', 401);
        $relative = $this->request->getGet('path') ?? '';
        try { $absPath = $this->safePath($relative); } catch (\RuntimeException $e) { return $this->jsonError('Invalid path.'); }
        if (!is_dir($absPath)) return $this->jsonError('Folder not found.', 404);
        return $this->jsonOk(['path' => $relative, 'folders' => $this->scanFolders($relative), 'files' => $this->scanFiles($relative)]);
    }

    // ── CREATE FOLDER ─────────────────────────────────────────────────────────

    public function createFolder()
    {
        if (!session()->get('logged_in')) return $this->jsonError('Unauthenticated.', 401);
        $privs = $this->privs();
        if (!($privs['folders_add'] ?? false)) return $this->jsonError('No permission to create folders.');

        $folderName = $this->sanitiseName($this->request->getPost('folder_name') ?? '');
        $parentPath = $this->request->getPost('parent_path') ?? '';
        if (!$folderName) return $this->jsonError('Folder name is required.');

        try { $newPath = $this->safePath(trim($parentPath . '/' . $folderName, '/')); } catch (\RuntimeException $e) { return $this->jsonError('Invalid path.'); }
        if (is_dir($newPath))        return $this->jsonError('A folder with that name already exists.');
        if (!mkdir($newPath, 0755, true)) return $this->jsonError('Failed to create folder.');

        return $this->jsonOk(['folder_name' => $folderName, 'folder_path' => trim($parentPath . '/' . $folderName, '/')]);
    }

    // ── UPLOAD SINGLE FILE ────────────────────────────────────────────────────

    public function upload()
    {
        if (!session()->get('logged_in')) return $this->jsonError('Unauthenticated.', 401);
        $privs = $this->privs();
        if (!($privs['records_upload'] ?? false)) return $this->jsonError('No permission to upload files.');

        $file       = $this->request->getFile('record_file');
        $folderPath = $this->request->getPost('folder_path') ?? '';

        if (!$file || !$file->isValid()) return $this->jsonError('No valid file received.');
        if ($file->hasMoved())           return $this->jsonError('File already processed.');
        if ($file->getSize() > $this->maxBytes) return $this->jsonError('File exceeds the 10 MB limit.');

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, $this->allowedExts)) return $this->jsonError('File type not allowed. Accepted: ' . implode(', ', $this->allowedExts));

        $safeName = time() . '_' . $this->sanitiseName($file->getClientName());
        try { $destDir = $this->safePath($folderPath); } catch (\RuntimeException $e) { return $this->jsonError('Invalid destination path.'); }
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        $file->move($destDir, $safeName);

        $filePath = trim($folderPath . '/' . $safeName, '/');
        return $this->jsonOk([
            'file_name'    => $safeName,
            'file_path'    => $filePath,
            'file_size'    => $this->formatBytes(filesize($destDir . DIRECTORY_SEPARATOR . $safeName)),
            'download_url' => base_url('academic-records/download?path=' . urlencode($filePath)),
            'preview_url'  => base_url('academic-records/preview?path='  . urlencode($filePath)),
        ]);
    }

    // ── UPLOAD MULTIPLE FILES ─────────────────────────────────────────────────

    public function uploadFolder()
    {
        if (!session()->get('logged_in')) return $this->jsonError('Unauthenticated.', 401);
        $privs = $this->privs();
        if (!($privs['records_upload'] ?? false)) return $this->jsonError('No permission to upload files.');

        $files      = $this->request->getFiles();
        $folderPath = $this->request->getPost('folder_path') ?? '';
        $uploaded   = []; $errors = [];

        if (empty($files['folder_files'])) return $this->jsonError('No files received.');
        try { $destDir = $this->safePath($folderPath); } catch (\RuntimeException $e) { return $this->jsonError('Invalid destination path.'); }
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);

        foreach ($files['folder_files'] as $file) {
            if (!$file->isValid() || $file->hasMoved()) continue;
            if ($file->getSize() > $this->maxBytes) { $errors[] = $file->getClientName() . ' exceeds 10 MB.'; continue; }
            $ext = strtolower($file->getClientExtension());
            if (!in_array($ext, $this->allowedExts)) { $errors[] = $file->getClientName() . ' — type not allowed.'; continue; }
            $safeName = time() . '_' . $this->sanitiseName($file->getClientName());
            $file->move($destDir, $safeName);
            $uploaded[] = $safeName;
        }

        return $this->jsonOk(['uploaded' => $uploaded, 'errors' => $errors]);
    }

    // ── PREVIEW — inline, renders PDF/image in browser ────────────────────────

    public function preview()
    {
        if (!session()->get('logged_in')) return redirect()->to('/login');
        $privs = $this->privs();
        if (!($privs['files_view'] ?? false)) return $this->response->setStatusCode(403)->setBody('No permission to view files.');

        $relative = $this->request->getGet('path') ?? '';
        try { $absPath = $this->safePath($relative); } catch (\RuntimeException $e) { return $this->response->setStatusCode(400)->setBody('Invalid path.'); }
        if (!is_file($absPath)) return $this->response->setStatusCode(404)->setBody('File not found.');

        return $this->response
            ->setHeader('Content-Type', mime_content_type($absPath))
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($absPath) . '"')
            ->setHeader('Content-Length', (string) filesize($absPath))
            ->setBody(file_get_contents($absPath));
    }

    // ── DOWNLOAD — forces save-as ─────────────────────────────────────────────

    public function download()
    {
        if (!session()->get('logged_in')) return redirect()->to('/login');
        $privs = $this->privs();
        if (!($privs['files_view'] ?? false)) return redirect()->back()->with('error', 'No permission to download files.');

        $relative = $this->request->getGet('path') ?? '';
        try { $absPath = $this->safePath($relative); } catch (\RuntimeException $e) { return redirect()->back()->with('error', 'Invalid file path.'); }
        if (!is_file($absPath)) return redirect()->back()->with('error', 'File not found.');

        return $this->response
            ->setHeader('Content-Type', mime_content_type($absPath))
            ->setHeader('Content-Disposition', 'attachment; filename="' . basename($absPath) . '"')
            ->setHeader('Content-Length', (string) filesize($absPath))
            ->setBody(file_get_contents($absPath));
    }

    // ── DELETE FILE ───────────────────────────────────────────────────────────

    public function deleteFile()
    {
        if (!session()->get('logged_in')) return $this->jsonError('Unauthenticated.', 401);
        $privs = $this->privs();
        if (!($privs['records_delete'] ?? false)) return $this->jsonError('No permission to delete files.');

        $relative = $this->request->getPost('path') ?? '';
        try { $absPath = $this->safePath($relative); } catch (\RuntimeException $e) { return $this->jsonError('Invalid path.'); }
        if (!is_file($absPath)) return $this->jsonError('File not found.', 404);
        if (!unlink($absPath))  return $this->jsonError('Delete failed.');
        return $this->jsonOk(['message' => 'File deleted.']);
    }

    // ── DELETE FOLDER ─────────────────────────────────────────────────────────

    public function deleteFolder()
    {
        if (!session()->get('logged_in')) return $this->jsonError('Unauthenticated.', 401);
        $privs = $this->privs();
        if (!($privs['folders_delete'] ?? false)) return $this->jsonError('No permission to delete folders.');

        $relative = $this->request->getPost('path') ?? '';
        try { $absPath = $this->safePath($relative); } catch (\RuntimeException $e) { return $this->jsonError('Invalid path.'); }
        if (!is_dir($absPath)) return $this->jsonError('Folder not found.', 404);
        if (rtrim($absPath, DIRECTORY_SEPARATOR) === rtrim(realpath($this->uploadRoot), DIRECTORY_SEPARATOR)) return $this->jsonError('Cannot delete the root folder.');
        $this->rmdirRecursive($absPath);
        return $this->jsonOk(['message' => 'Folder deleted.']);
    }

    private function rmdirRecursive(string $dir): void
    {
        foreach (scandir($dir) as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $full = $dir . DIRECTORY_SEPARATOR . $entry;
            is_dir($full) ? $this->rmdirRecursive($full) : unlink($full);
        }
        rmdir($dir);
    }

    // ── RENAME ────────────────────────────────────────────────────────────────

    public function rename()
    {
        if (!session()->get('logged_in')) return $this->jsonError('Unauthenticated.', 401);
        $privs = $this->privs();
        if (!($privs['records_organize'] ?? false)) return $this->jsonError('No permission to rename items.');

        $relative = $this->request->getPost('path')     ?? '';
        $newName  = $this->sanitiseName($this->request->getPost('new_name') ?? '');
        if (!$newName) return $this->jsonError('New name is required.');

        try { $absOld = $this->safePath($relative); } catch (\RuntimeException $e) { return $this->jsonError('Invalid path.'); }
        if (!file_exists($absOld)) return $this->jsonError('Item not found.', 404);

        $absNew = dirname($absOld) . DIRECTORY_SEPARATOR . $newName;
        if (file_exists($absNew))    return $this->jsonError('An item with that name already exists.');
        if (!rename($absOld, $absNew)) return $this->jsonError('Rename failed.');

        return $this->jsonOk(['new_path' => trim(dirname($relative) . '/' . $newName, '/'), 'new_name' => $newName]);
    }

    // ── MOVE ──────────────────────────────────────────────────────────────────

    public function move()
    {
        if (!session()->get('logged_in')) return $this->jsonError('Unauthenticated.', 401);
        $privs = $this->privs();
        if (!($privs['records_organize'] ?? false)) return $this->jsonError('No permission to move items.');

        $srcRelative  = $this->request->getPost('src_path')  ?? '';
        $destRelative = $this->request->getPost('dest_path') ?? '';

        try { $absSrc = $this->safePath($srcRelative); $absDest = $this->safePath($destRelative); }
        catch (\RuntimeException $e) { return $this->jsonError('Invalid path.'); }

        if (!file_exists($absSrc)) return $this->jsonError('Source not found.', 404);
        if (!is_dir($absDest))     return $this->jsonError('Destination folder not found.', 404);

        $destFull = $absDest . DIRECTORY_SEPARATOR . basename($absSrc);
        if (file_exists($destFull))       return $this->jsonError('An item with that name already exists in the destination.');
        if (!rename($absSrc, $destFull))  return $this->jsonError('Move failed.');

        return $this->jsonOk(['new_path' => trim($destRelative . '/' . basename($absSrc), '/')]);
    }

    // ================================================================
// PHASE 1: TEMPORARY UPLOAD
// ================================================================

/**
 * Handle temporary file upload (before preview)
 * Route: POST /academic-records/temp-upload
 */
public function tempUpload()
{
    if (!session()->get('logged_in')) {
        return $this->jsonError('Unauthenticated.', 401);
    }

    $privs = $this->privs();
    if (!($privs['records_upload'] ?? false)) {
        return $this->jsonError('No permission to upload files.');
    }

    $file       = $this->request->getFile('record_file');
    $folderPath = $this->request->getPost('folder_path') ?? '';

    // Validate file
    if (!$file || !$file->isValid()) {
        return $this->jsonError('No valid file received.');
    }
    if ($file->hasMoved()) {
        return $this->jsonError('File already processed.');
    }
    if ($file->getSize() > $this->maxBytes) {
        return $this->jsonError('File exceeds the 10 MB limit.');
    }

    $ext = strtolower($file->getClientExtension());
    if (!in_array($ext, $this->allowedExts)) {
        return $this->jsonError('File type not allowed. Accepted: ' . implode(', ', $this->allowedExts));
    }

    // Create temp directory if not exists
    $tempDir = WRITEPATH . 'temp_uploads';
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    // Generate unique token
    $token = bin2hex(random_bytes(16));
    $safeName = $token . '_' . $this->sanitiseName($file->getClientName());
    
    // Move to temp location
    if (!$file->move($tempDir, $safeName)) {
        return $this->jsonError('Failed to save temporary file.');
    }

    // Store metadata in session
    session()->set('temp_upload_' . $token, [
        'filename' => $safeName,
        'original_name' => $file->getClientName(),
        'folder_path' => $folderPath,
        'size' => $file->getSize(),
        'ext' => $ext,
        'uploaded_at' => time()
    ]);

    return $this->jsonOk([
        'token' => $token,
        'preview_url' => base_url('academic-records/preview-pending/' . $token),
        'original_name' => $file->getClientName(),
        'size' => $this->formatBytes($file->getSize())
    ]);
}

// ================================================================
// PHASE 2: PREVIEW PENDING FILE
// ================================================================

/**
 * Preview a pending (temporary) file
 * Route: GET /academic-records/preview-pending/{token}
 */
public function previewPending($token)
{
    if (!session()->get('logged_in')) {
        return $this->response->setStatusCode(401)->setBody('Unauthenticated.');
    }

    $privs = $this->privs();
    if (!($privs['files_view'] ?? false)) {
        return $this->response->setStatusCode(403)->setBody('No permission to view files.');
    }

    // Get metadata from session
    $metadata = session()->get('temp_upload_' . $token);
    if (!$metadata) {
        return $this->response->setStatusCode(404)->setBody('Temporary file not found or expired.');
    }

    $tempPath = WRITEPATH . 'temp_uploads/' . $metadata['filename'];
    if (!is_file($tempPath)) {
        return $this->response->setStatusCode(404)->setBody('Temporary file not found.');
    }

    // Return file for preview
    return $this->response
        ->setHeader('Content-Type', mime_content_type($tempPath))
        ->setHeader('Content-Disposition', 'inline; filename="' . $metadata['original_name'] . '"')
        ->setHeader('Content-Length', (string) filesize($tempPath))
        ->setBody(file_get_contents($tempPath));
}

/**
 * Download a pending (temporary) file
 * Route: GET /academic-records/download-pending/{token}
 */
public function downloadPending($token)
{
    if (!session()->get('logged_in')) {
        return redirect()->to('/login');
    }

    $privs = $this->privs();
    if (!($privs['files_view'] ?? false)) {
        return $this->response->setStatusCode(403)->setBody('No permission to download files.');
    }

    $metadata = session()->get('temp_upload_' . $token);
    if (!$metadata) {
        return $this->response->setStatusCode(404)->setBody('Temporary file not found or expired.');
    }

    $tempPath = WRITEPATH . 'temp_uploads/' . $metadata['filename'];
    if (!is_file($tempPath)) {
        return $this->response->setStatusCode(404)->setBody('Temporary file not found.');
    }

    return $this->response
        ->setHeader('Content-Type', mime_content_type($tempPath))
        ->setHeader('Content-Disposition', 'attachment; filename="' . $metadata['original_name'] . '"')
        ->setHeader('Content-Length', (string) filesize($tempPath))
        ->setBody(file_get_contents($tempPath));
}

/**
 * Cancel pending upload and delete temp file
 * Route: POST /academic-records/cancel-pending
 */
public function cancelPending()
{
    if (!session()->get('logged_in')) {
        return $this->jsonError('Unauthenticated.', 401);
    }

    $token = $this->request->getPost('token');
    if (!$token) {
        return $this->jsonError('Token required.');
    }

    $metadata = session()->get('temp_upload_' . $token);
    if ($metadata) {
        $tempPath = WRITEPATH . 'temp_uploads/' . $metadata['filename'];
        if (is_file($tempPath)) {
            unlink($tempPath);
        }
        session()->remove('temp_upload_' . $token);
    }

    return $this->jsonOk(['message' => 'Upload cancelled.']);
}

// ================================================================
// PHASE 3: FOLDER BROWSER (Uses existing listFolder method)
// ================================================================
// Note: Your existing listFolder() method already provides the folder
// structure. The frontend will use it to populate the folder browser modal.

// ================================================================
// PHASE 4: FINALIZE SAVE
// ================================================================

/**
 * Finalize upload - move from temp to permanent location
 * Route: POST /academic-records/finalize-upload
 */
public function finalizeUpload()
{
    if (!session()->get('logged_in')) {
        return $this->jsonError('Unauthenticated.', 401);
    }

    $privs = $this->privs();
    if (!($privs['records_upload'] ?? false)) {
        return $this->jsonError('No permission to upload files.');
    }

    $token = $this->request->getPost('token');
    $folderPath = $this->request->getPost('folder_path') ?? '';

    if (!$token) {
        return $this->jsonError('Token required.');
    }

    // Get temp file metadata
    $metadata = session()->get('temp_upload_' . $token);
    if (!$metadata) {
        return $this->jsonError('Temporary file not found or expired.');
    }

    $tempPath = WRITEPATH . 'temp_uploads/' . $metadata['filename'];
    if (!is_file($tempPath)) {
        return $this->jsonError('Temporary file not found.');
    }

    // Sanitize destination folder
    try {
        $destDir = $this->safePath($folderPath);
    } catch (\RuntimeException $e) {
        return $this->jsonError('Invalid destination path.');
    }

    // Create destination directory if needed
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }

    // Generate final filename with timestamp
    $finalName = time() . '_' . $this->sanitiseName($metadata['original_name']);
    $finalPath = $destDir . DIRECTORY_SEPARATOR . $finalName;

    // Move file from temp to permanent location
    if (!rename($tempPath, $finalPath)) {
        return $this->jsonError('Failed to save file to permanent location.');
    }

    // Clean up session
    session()->remove('temp_upload_' . $token);

    // Build file info for response
    $relativePath = trim($folderPath . '/' . $finalName, '/');
    
    return $this->jsonOk([
        'message' => 'Record saved successfully',
        'file_name' => $finalName,
        'file_path' => $relativePath,
        'file_size' => $this->formatBytes(filesize($finalPath)),
        'download_url' => base_url('academic-records/download?path=' . urlencode($relativePath)),
        'preview_url' => base_url('academic-records/preview?path=' . urlencode($relativePath))
    ]);
}

/**
 * Clean up old temporary files (maintenance)
 * Can be called via cron or manually
 */
public function cleanupTempFiles()
{
    $tempDir = WRITEPATH . 'temp_uploads';
    if (!is_dir($tempDir)) {
        return;
    }

    $maxAge = 3600; // 1 hour
    $now = time();

    foreach (scandir($tempDir) as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $filePath = $tempDir . DIRECTORY_SEPARATOR . $file;
        if (is_file($filePath) && ($now - filemtime($filePath)) > $maxAge) {
            unlink($filePath);
        }
    }
}
}