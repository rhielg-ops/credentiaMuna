<?php

namespace App\Models;

use CodeIgniter\Model;

class FileMetadataModel extends Model
{
    protected $table         = 'file_metadata';
    protected $primaryKey    = 'metadata_id';  // ✅ FIXED from 'id'
    protected $useTimestamps = true;

    protected $allowedFields = [
        'filename', 'original_name', 'file_path',
        'folder_path', 'student_name', 'student_id',
        'document_type_id', 'extracted_text', 'file_hash',
        'file_size', 'mime_type', 'uploaded_by',
    ];

    public function getFilesInFolder(string $folder): array
    {
        return $this->where('folder_path', $folder)->findAll();
    }

    public function findByHash(string $sha256Hash): ?array
    {
        return $this->where('file_hash', $sha256Hash)->first();
    }

    public function searchContent(string $query, int $limit = 50): array
    {
        $db = \Config\Database::connect();
        return $db->query(
            "SELECT * FROM file_metadata
             WHERE MATCH(extracted_text) AGAINST (? IN BOOLEAN MODE)
                OR student_name LIKE ?
             LIMIT ?",
            ['+' . $query, '%' . $query . '%', $limit]
        )->getResultArray();  // ✅ FIXED — removed broken code, uncommented correct version
    }
}