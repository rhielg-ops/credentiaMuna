<?php
namespace App\Models;

use CodeIgniter\Model;

class RecordTypeModel extends Model
{
    protected $table      = 'record_types';
    protected $primaryKey = 'type_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'key_name', 'label', 'filename_suffix', 'is_active', 'sort_order',
    ];
    protected $useTimestamps = true;

    /**
     * Returns all active types as [ key_name => ['label'=>..., 'suffix'=>...] ]
     * Result is cached in a static property for the request lifetime.
     */
    private static array $_cache = [];

    public function getAllActive(): array
    {
        if (!empty(self::$_cache)) return self::$_cache;

        $rows = $this->where('is_active', 1)
                     ->orderBy('sort_order', 'ASC')
                     ->findAll();

        self::$_cache = [];
        foreach ($rows as $row) {
            self::$_cache[$row['key_name']] = [
                'type_id' => $row['type_id'],
                'label'   => $row['label'],
                'suffix'  => $row['filename_suffix'],
            ];
        }
        return self::$_cache;
    }

    /**
     * Returns keyword map: [ key_name => [keyword, keyword, ...] ]
     */
    public function getKeywordMap(): array
    {
        $db   = \Config\Database::connect();
        $rows = $db->table('record_types rt')
            ->select('rt.key_name, rk.keyword')
            ->join('record_keywords rk', 'rk.type_id = rt.type_id')
            ->where('rt.is_active', 1)
            ->orderBy('rt.sort_order', 'ASC')
            ->get()->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['key_name']][] = $row['keyword'];
        }
        return $map;
    }

    /**
     * Convenience: returns [ key_name => filename_suffix ]
     */
    public function getSuffixMap(): array
    {
        $types = $this->getAllActive();
        return array_map(fn($t) => $t['suffix'], $types);
    }

    public static function clearCache(): void
    {
        self::$_cache = [];
    }
}