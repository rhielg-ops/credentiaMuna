<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPrivilegeModel extends Model
{
    protected $table = 'user_privileges';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id',
        'privilege_key',
        'privilege_value'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all privileges for a user
     */
    public function getUserPrivileges($userId)
    {
        $privileges = $this->where('user_id', $userId)->findAll();
        
        $result = [];
        foreach ($privileges as $priv) {
            $result[$priv['privilege_key']] = (bool) $priv['privilege_value'];
        }
        
        return $result;
    }

    /**
     * Check if user has a specific privilege
     */
    public function hasPrivilege($userId, $privilegeKey)
    {
        $privilege = $this->where('user_id', $userId)
                          ->where('privilege_key', $privilegeKey)
                          ->first();
        
        return $privilege ? (bool) $privilege['privilege_value'] : false;
    }

    /**
     * Set privilege for user
     */
    public function setPrivilege($userId, $privilegeKey, $value = true)
    {
        $existing = $this->where('user_id', $userId)
                         ->where('privilege_key', $privilegeKey)
                         ->first();
        
        if ($existing) {
            return $this->update($existing['id'], ['privilege_value' => $value ? 1 : 0]);
        } else {
            return $this->insert([
                'user_id' => $userId,
                'privilege_key' => $privilegeKey,
                'privilege_value' => $value ? 1 : 0
            ]);
        }
    }

    /**
     * Set multiple privileges for user
     */
    public function setPrivileges($userId, array $privileges)
    {
        $success = true;
        
        foreach ($privileges as $key => $value) {
            if (!$this->setPrivilege($userId, $key, $value)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Get default privileges for a role
     */
    public function getDefaultPrivileges($role)
    {
        $defaults = [
            'admin' => [
                'records_upload'   => true,
                'files_view'       => true,
                'records_update'   => true,
                'records_organize' => true,
                'folders_add'      => true,
                'records_delete'   => true,
                'folders_delete'   => true,
                'profile_edit'     => true,
                'user_management'  => true,
                'system_backup'    => true,
                'audit_logs'       => true,
                'full_admin'       => true,
            ],
            'user' => [
                'records_upload'   => true,
                'files_view'       => true,
                'records_update'   => false,
                'records_organize' => false,
                'folders_add'      => false,
                'records_delete'   => false,
                'folders_delete'   => false,
                'profile_edit'     => false,
                'user_management'  => false,
                'system_backup'    => false,
                'audit_logs'       => false,
                'full_admin'       => false,
            ],
        ];

        return $defaults[$role] ?? [];
    }

    /**
     * Initialize default privileges for new user
     */
    public function initializeUserPrivileges($userId, $role)
    {
        $privileges = $this->getDefaultPrivileges($role);
        return $this->setPrivileges($userId, $privileges);
    }

    /**
     * Delete all privileges for a user
     */
    public function deleteUserPrivileges($userId)
    {
        return $this->where('user_id', $userId)->delete();
    }

    /**
     * Get available privilege definitions
     */
    public function getPrivilegeDefinitions()
    {
        return [
            'records_upload' => [
                'label'       => 'Upload Records',
                'description' => 'Upload digitized academic records',
                'category'    => 'Records Management',
            ],
            'files_view' => [
                'label'       => 'View Files',
                'description' => 'View, download, and print archived records',
                'category'    => 'Records Management',
            ],
            'records_update' => [
                'label'       => 'Update Records',
                'description' => 'Replace existing files with new versions',
                'category'    => 'Records Management',
            ],
            'records_organize' => [
                'label'       => 'Organize Records',
                'description' => 'Move files/folders, rename files, and manage file structure',
                'category'    => 'Records Management',
            ],
            'folders_add' => [
                'label'       => 'Add Folders',
                'description' => 'Create new folders or categories',
                'category'    => 'Records Management',
            ],
            'records_delete' => [
                'label'       => 'Delete Records',
                'description' => 'Delete archived files',
                'category'    => 'Records Management',
            ],
            'folders_delete' => [
                'label'       => 'Delete Folders',
                'description' => 'Delete folders or categories',
                'category'    => 'Records Management',
            ],
            'profile_edit' => [
                'label'       => 'Edit Profile',
                'description' => 'Edit user profile and personal settings',
                'category'    => 'Profile Management',
            ],
            'user_management' => [
                'label'       => 'Manage Users',
                'description' => 'Add, edit, and assign roles to users',
                'category'    => 'Administration',
            ],
            'system_backup' => [
                'label'       => 'System Backup',
                'description' => 'Access system backup and restore features',
                'category'    => 'Administration',
            ],
            'audit_logs' => [
                'label'       => 'Audit Logs',
                'description' => 'View system activity logs',
                'category'    => 'Administration',
            ],
            'full_admin' => [
                'label'       => 'Full Admin Access',
                'description' => 'Automatically grants all privileges when selected',
                'category'    => 'Administration',
            ],
        ];
    }
}