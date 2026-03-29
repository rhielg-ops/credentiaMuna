<?php

namespace App\Models;

use CodeIgniter\Model;

class GroupPrivilegeModel extends Model
{
    protected $table      = 'group_privileges';
    protected $primaryKey = 'gprivilege_id';
    protected $returnType = 'array';

    protected $allowedFields = ['role_id', 'privilege_key', 'group_modules'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    /** All privileges for a role_id, keyed by privilege_key */
    public function getByRole(int $roleId): array
    {
        $rows   = $this->where('role_id', $roleId)->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['privilege_key']] = $row['group_modules'] ?? '';
        }
        return $result;
    }

    /**
     * Full matrix for the Group Privileges modal.
     * Returns: [ 'admin' => ['privilege_key' => 'module', ...], 'user' => [...] ]
     */
    public function getFullMatrix(): array
    {
        $db   = \Config\Database::connect();
        $rows = $db->table('group_privileges gp')
            ->select('r.role_name, gp.privilege_key, gp.group_modules')
            ->join('roles r', 'r.role_id = gp.role_id')
            ->get()->getResultArray();

        $matrix = [];
        foreach ($rows as $row) {
            $matrix[$row['role_name']][$row['privilege_key']] = $row['group_modules'];
        }
        return $matrix;
    }
}
