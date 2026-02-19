<?php

namespace App\Models;

use CodeIgniter\Model;

class FileMetadataModel extends Model
{
    protected $table      = 'file_metadata';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'filename', 'original_name', 'nextcloud_path',
        'folder_path', 'file_size', 'mime_type', 'uploaded_by'
    ];
    protected $useTimestamps = true;

    public function getFilesInFolder(string $folder): array
    {
        return $this->where('folder_path', $folder)->findAll();
    }
}