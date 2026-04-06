<?php

namespace App\Controllers;

use App\Models\UserPrivilegeModel;
use App\Services\OcrService;

class AcademicRecords extends BaseController
{
    private string $uploadRoot;
    private array  $allowedExts = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    private int    $maxBytes    = 10 * 1024 * 1024;

    protected UserPrivilegeModel $privilegeModel;
    protected \App\Models\UserFolderAccessModel $folderAccessModel;
    protected \App\Models\FileMetadataModel     $fileMetadataModel;
    protected \App\Models\FileAccessLogModel    $fileAccessLogModel;


    public function __construct()
    {
        $this->uploadRoot        = FCPATH . 'uploads/academic_records/';
        $this->privilegeModel    = new UserPrivilegeModel();
        $this->folderAccessModel = new \App\Models\UserFolderAccessModel();
        $this->fileMetadataModel = new \App\Models\FileMetadataModel();
        $this->fileAccessLogModel= new \App\Models\FileAccessLogModel();


        if (!is_dir($this->uploadRoot)) {
            mkdir($this->uploadRoot, 0755, true);
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function privs(): array
     {
         // Admins always have full privileges regardless of DB rows.
         // This mirrors canAccessPath() which already bypasses the DB for admins,
         // and prevents breakage when a privilege row is missing for an admin account.
         if (session()->get('role') === 'admin') {
             return [
                 'records_upload'   => true,
                 'files_view'       => true,
                 'records_organize' => true,
                 'folders_add'      => true,
                 'records_delete'   => true,
                 'folders_delete'   => true,
                 'profile_edit'     => true,
                 'user_management'  => true,
                 'system_backup'    => true,
                 'audit_logs'       => true,
                 'full_admin'       => true,
             ];
         }
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

    /**
     * Admins (role === 'admin') always pass.
     * Regular users must have the path in their assigned folders.
     * Empty path (root) is only allowed for admins.
     */
    private function canAccessPath(string $relativePath): bool
    {
        if (session()->get('role') === 'admin') return true;

        // Root path "" is always allowed — scanFolders() handles
        // filtering so the user only SEES their assigned folders.
        // Blocking root here causes "Access denied" on initial page load.
        if (ltrim($relativePath, '/') === '') return true;

        $userId = (int) session()->get('user_id');
        return $this->folderAccessModel->canAccessPath($userId, $relativePath);
    }


    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    // ── FIX: single scandir pass — returns both folders AND files at once ────
    // Old code called scandir() twice (once in scanFolders, once in scanFiles)
    // AND did a third scandir() per sub-folder just to count its files.
    // Now: one pass, one open, zero extra reads.
    private function scanDirectory(string $relative): array
    {
        $abs = $this->uploadRoot . ltrim($relative, '/');
        if (!is_dir($abs)) return ['folders' => [], 'files' => []];

        $isAdmin = session()->get('role') === 'admin';
        $allowed = $isAdmin ? [] : $this->folderAccessModel->getAllowedFolders(
            (int) session()->get('user_id')
        );

        $folders = [];
        $files   = [];

        // Single pass over the directory entries
        $entries = scandir($abs, SCANDIR_SORT_NONE); // skip default sort — we sort ourselves
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $full = $abs . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($full)) {
                $entryPath = trim($relative . '/' . $entry, '/');

                if (!$isAdmin) {
                    $visible = false;
                    foreach ($allowed as $af) {
                        $af = ltrim($af, '/');
                        if ($entryPath === $af
                            || str_starts_with($entryPath, $af . '/')
                            || str_starts_with($af, $entryPath . '/')) {
                            $visible = true; break;
                        }
                    }
                    if (!$visible) continue;
                }

                // FIX: count files without a second scandir() — use glob count instead
                // glob with GLOB_NOSORT is faster than scandir+filter on large dirs
                $fileCount = count(glob($full . DIRECTORY_SEPARATOR . '*.*', GLOB_NOSORT));

                $folders[] = [
                    'name'     => $entry,
                    'path'     => $entryPath,
                    'modified' => date('Y-m-d', filemtime($full)),
                    'count'    => $fileCount,
                ];
            } elseif (is_file($full) && $this->canAccessPath($relative)) {
                $ext     = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                $relPath = trim($relative . '/' . $entry, '/');
                $files[] = [
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

        usort($folders, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        usort($files,   fn($a, $b) => strcasecmp($a['name'], $b['name']));
        return ['folders' => $folders, 'files' => $files];
    }

    // Kept for any callers outside this controller; delegates to scanDirectory
    private function scanFolders(string $relative): array
    {
        return $this->scanDirectory($relative)['folders'];
    }

    private function scanFiles(string $relative): array
    {
        return $this->scanDirectory($relative)['files'];
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────

    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to('/login')->with('error', 'Please log in first.');

        $privs = $this->privs();

        // FIX: Do NOT pre-scan folders/files here.
        // The view's JS calls listFolder('') via AJAX immediately on load,
        // so doing scanFolders('') + scanFiles('') here was a wasted double-scan
        // on every page request. The JS handles the initial folder load.
        return view('auth/academic_records', [
            'email'    => session()->get('email'),
            'role'     => session()->get('role'),
            'fullName' => session()->get('full_name') ?? '',

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

        if (!$this->canAccessPath($relative)) return $this->jsonError('Access denied.', 403);

        // FIX: single scanDirectory call replaces two separate scandir() passes
        $dir = $this->scanDirectory($relative);
        return $this->jsonOk(['path' => $relative, 'folders' => $dir['folders'], 'files' => $dir['files']]);
    }

    // ── LIST ALL FOLDERS (AJAX) — for search index + move modal ──────────────
    // Returns the complete flat folder list in ONE request instead of the
    // JS _crawlUnified() doing N recursive listFolder() calls (one per folder).

    public function listAllFolders()
    {
        if (!session()->get('logged_in')) return $this->jsonError('Unauthenticated.', 401);

        $isAdmin = session()->get('role') === 'admin';
        $allowed = $isAdmin ? [] : $this->folderAccessModel->getAllowedFolders(
            (int) session()->get('user_id')
        );

        $folders = [];
        $this->_collectAllFolders('', [], $isAdmin, $allowed, $folders);

        return $this->jsonOk(['folders' => $folders]);
    }

    private function _collectAllFolders(
        string $relative,
        array  $ancestorLabels,
        bool   $isAdmin,
        array  $allowed,
        array  &$result
    ): void {
        $abs = $this->uploadRoot . ltrim($relative, '/');
        if (!is_dir($abs)) return;

        $locationLabel = $ancestorLabels
            ? 'Academic Records › ' . implode(' › ', $ancestorLabels)
            : 'Academic Records';

        $entries = scandir($abs, SCANDIR_SORT_NONE);
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $full      = $abs . DIRECTORY_SEPARATOR . $entry;
            if (!is_dir($full)) continue;

            $entryPath = trim(($relative ? $relative . '/' : '') . $entry, '/');

            if (!$isAdmin) {
                $visible = false;
                foreach ($allowed as $af) {
                    $af = ltrim($af, '/');
                    if ($entryPath === $af
                        || str_starts_with($entryPath, $af . '/')
                        || str_starts_with($af, $entryPath . '/')) {
                        $visible = true; break;
                    }
                }
                if (!$visible) continue;
            }

            $result[] = [
                'kind'          => 'folder',
                'name'          => $entry,
                'path'          => $entryPath,
                'parentPath'    => $relative,
                'locationLabel' => $locationLabel,
                'modified'      => date('Y-m-d', filemtime($full)),
                'count'         => count(glob($full . DIRECTORY_SEPARATOR . '*.*', GLOB_NOSORT)),
            ];

            // Recurse
            $this->_collectAllFolders($entryPath, [...$ancestorLabels, $entry], $isAdmin, $allowed, $result);
        }
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
        if (!$this->canAccessPath($parentPath)) return $this->jsonError('Access denied.', 403);
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
        if (!$this->canAccessPath($folderPath)) return $this->jsonError('Access denied.', 403);
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
        if (!$this->canAccessPath($folderPath)) return $this->jsonError('Access denied.', 403);
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

        if (!$this->canAccessPath($relative)) return $this->response->setStatusCode(403)->setBody('Access denied.');

        $this->fileAccessLogModel->log($relative, 'preview');

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

        if (!$this->canAccessPath($relative)) return redirect()->back()->with('error', 'Access denied.');

        $this->fileAccessLogModel->log($relative, 'download');

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
        if (!$this->canAccessPath($relative)) return $this->jsonError('Access denied.', 403);
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
        if (!$this->canAccessPath($relative)) return $this->jsonError('Access denied.', 403);
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
        if (!$this->canAccessPath($relative)) return $this->jsonError('Access denied.', 403);

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
        if (!$this->canAccessPath($srcRelative))  return $this->jsonError('Access denied.', 403);
        if (!$this->canAccessPath($destRelative)) return $this->jsonError('Access denied to destination.', 403);

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

    // Run OCR on the temp file (failure does NOT abort the upload)
$ocrResult = [];
// Run text extraction on all supported file types including docx
if (in_array($ext, ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'])) {
    $ocrService = new OcrService();
    $ocrResult  = $ocrService->extractFromFile(
        $tempDir . DIRECTORY_SEPARATOR . $safeName,
        $file->getClientName()
    );
}

// Store metadata in session (includes OCR result)
$tempFilePath = $tempDir . DIRECTORY_SEPARATOR . $safeName;

session()->set('temp_upload_' . $token, [
    'filename'      => $safeName,
    'original_name' => $file->getClientName(),
    'folder_path'   => $folderPath,
    'size'          => $file->getSize(),
    'ext'           => $ext,
    'uploaded_at'   => time(),
    'ocr'           => $ocrResult,
    'file_hash'     => $ocrResult['file_hash'] ?? hash_file('sha256', $tempFilePath),
]);


return $this->jsonOk([
    'token'           => $token,
    'preview_url'     => base_url('academic-records/preview-pending/' . $token),
    'original_name'   => $file->getClientName(),
    'size'            => $this->formatBytes($file->getSize()),
    'ocr_success'     => $ocrResult['success']        ?? false,
    'ocr_text'        => $ocrResult['text']           ?? '',
    'ocr_suggestions' => $ocrResult['suggestions']    ?? ['folder' => '', 'filename' => ''],
    'file_hash'       => $ocrResult['file_hash']      ?? null,
    'ocr_confidence'  => $ocrResult['ocr_confidence'] ?? null,
]);
}

public function getOcrResult(string $token): \CodeIgniter\HTTP\ResponseInterface
{
    if (!session()->get('logged_in')) {
        return $this->jsonError('Unauthenticated.', 401);
    }
    $metadata = session()->get('temp_upload_' . $token);
    if (!$metadata) {
        return $this->jsonError('Token not found or expired.', 404);
    }
    $ocr = $metadata['ocr'] ?? [];
    return $this->jsonOk([
        'ocr_success'     => $ocr['success']     ?? false,
        'ocr_text'        => $ocr['text']        ?? '',
        'ocr_suggestions' => $ocr['suggestions'] ?? ['folder' => '', 'filename' => ''],
        'ocr_error'       => $ocr['error']       ?? null,
    ]);
}

public function testOcr()
{
    echo '<pre>';
    
    // Search entire project for recently modified jpg/png files
    $base = FCPATH . '..'; // project root
    echo 'Project root: ' . realpath($base) . "\n\n";
    
    $dirs = [
        WRITEPATH,
        sys_get_temp_dir(),
        FCPATH . 'uploads',
        ROOTPATH . 'uploads',
        ROOTPATH . 'storage',
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) { echo "NOT EXISTS: $dir\n"; continue; }
        echo "Scanning: $dir\n";
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (in_array(strtolower($file->getExtension()), ['jpg','jpeg','png'])) {
                echo '  FOUND: ' . $file->getPathname() . ' (' . $file->getSize() . ' bytes)' . "\n";
            }
        }
    }
    
    echo '</pre>';
    die();
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

   // Use confirmed filename from Edit Filename modal, or fall back to timestamped original name
$suggestedFilename = trim($this->request->getPost('suggested_filename') ?? '');
if ($suggestedFilename) {
    // Preserve the dot for extension — sanitize name and extension separately
    $nameOnly = pathinfo($suggestedFilename, PATHINFO_FILENAME);
    $extOnly  = pathinfo($suggestedFilename, PATHINFO_EXTENSION);
    $safeName = preg_replace('/[^\w\-]/', '_', $nameOnly);
    $finalName = $safeName . ($extOnly ? '.' . $extOnly : '.' . $metadata['ext']);
} else {
    $finalName = time() . '_' . $this->sanitiseName($metadata['original_name']);
}
$finalPath = $destDir . DIRECTORY_SEPARATOR . $finalName;
    // Move file from temp to permanent location
    // Falls back to copy+delete if rename() fails across different drives/partitions (Windows)
    if (!@rename($tempPath, $finalPath)) {
        if (!copy($tempPath, $finalPath)) {
            return $this->jsonError('Failed to save file to permanent location.');
        }
        unlink($tempPath); // delete temp only after successful copy
    }

    // ── Save metadata to file_metadata table ─────────────────────────
    $ocrData  = $metadata['ocr'] ?? [];
    $fileHash = $ocrData['file_hash'] ?? hash_file('sha256', $finalPath);

    // Duplicate detection: warn but still save (let admin decide)
    $duplicate = $this->fileMetadataModel->findByHash($fileHash);
    if ($duplicate) {
        log_message('notice', 'Duplicate file uploaded: ' . $finalName
            . ' matches existing ' . $duplicate['filename']);
        // You can add a session flash or JSON key here if you want to
        // surface this in the UI — for now it just logs.
    }

    $docTypeId = null;
    $docTypeKey = $ocrData['suggestions']['doc_type'] ?? '';
    if ($docTypeKey) {
        $typeModel = new \App\Models\RecordTypeModel();
        $types     = $typeModel->getAllActive();
        $docTypeId = $types[$docTypeKey]['type_id'] ?? null;
    }

    // ─────────────────────────────────────────────────────────────────


    // Build file info for response
    $relativePath = trim($folderPath . '/' . $finalName, '/');

    $this->fileMetadataModel->insert([
        'filename'         => $finalName,
        'original_name'    => $metadata['original_name'],
        'file_path'        => $relativePath,  // e.g. 2021/file.pdf
        'folder_path'      => $folderPath,
        'student_name'     => $ocrData['suggestions']['name'] ?? null,
        'student_id'       => null,  // not yet extracted by OCR — extend later
        'document_type_id' => $docTypeId,
        'extracted_text'   => $ocrData['text'] ?? null,
        'file_hash'        => $fileHash,
        'file_size'        => filesize($finalPath),
        'mime_type'        => mime_content_type($finalPath),
        'uploaded_by'      => (int) session()->get('user_id'),
    ]);

    // Clean up session
    session()->remove('temp_upload_' . $token);
    
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
 * AJAX: metadata + fulltext search across file_metadata table.
 * Route: GET academic-records/metadata-search?q=juan+tor
 *
 * Returns JSON list of matching files with path, type, student name.
 * The existing JS search uses filesystem crawl; this supplements it with
 * DB-indexed content (OCR text) search.
 */
public function metadataSearch(): \CodeIgniter\HTTP\ResponseInterface
{
    if (!session()->get('logged_in')) {
        return $this->jsonError('Unauthenticated.', 401);
    }

    $q = trim($this->request->getGet('q') ?? '');
    if (strlen($q) < 2) {
        return $this->jsonError('Query too short.', 400);
    }

    $db     = \Config\Database::connect();
    $userId = (int) session()->get('user_id');
    $isAdmin= session()->get('role') === 'admin';

    // Build base query
    $builder = $db->table('file_metadata fm')
        ->select('fm.*, rt.label AS document_type_label')
        ->join('record_types rt', 'rt.type_id = fm.document_type_id', 'left');

    // Non-admins: restrict to their assigned folder paths
    if (!$isAdmin) {
        $allowed = $this->folderAccessModel->getAllowedFolders($userId);
        if (empty($allowed)) {
            return $this->jsonOk(['results' => []]);
        }
        $builder->groupStart();
        foreach ($allowed as $folder) {
            $builder->orLike('fm.folder_path', $folder, 'after');
        }
        $builder->groupEnd();
    }

    // Search: student name OR fulltext on extracted text
    $builder->groupStart()
        ->like('fm.student_name', $q)
        ->orLike('fm.original_name', $q)
        ->orWhere("MATCH(fm.extracted_text) AGAINST (? IN BOOLEAN MODE)", '+' . $q)
        ->groupEnd();

    $results = $builder->limit(50)->get()->getResultArray();

    // Add download URL for each result
    foreach ($results as &$row) {
        $row['download_url'] = base_url('academic-records/download?path=' . urlencode($row['file_path']));
        $row['preview_url']  = base_url('academic-records/preview?path='  . urlencode($row['file_path']));
        unset($row['extracted_text']); // don't send full OCR text to browser
    }

    return $this->jsonOk(['results' => $results, 'total' => count($results)]);
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