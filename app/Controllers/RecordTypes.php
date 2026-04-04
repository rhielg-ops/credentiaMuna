<?php
namespace App\Controllers;

use App\Models\RecordTypeModel;

class RecordTypes extends BaseController
{
    protected RecordTypeModel $typeModel;

    public function __construct()
    {
        $this->typeModel = new RecordTypeModel();
    }

    private function requireAdmin(): ?object
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized.');
        }
        return null;
    }

    // GET  super-admin/record-types
    public function index()
    {
        if ($r = $this->requireAdmin()) return $r;

        $db    = \Config\Database::connect();
        $types = $this->typeModel->orderBy('sort_order', 'ASC')->findAll();

        // Attach keyword counts
        foreach ($types as &$t) {
            $t['keyword_count'] = $db->table('record_keywords')
                ->where('type_id', $t['type_id'])->countAllResults();
        }

        return view('super_admin/record_types', ['types' => $types]);
    }

    // POST super-admin/record-types/save-type
    public function saveType()
    {
        if ($r = $this->requireAdmin()) return $r;

        $id     = $this->request->getPost('type_id');
        $data   = [
            'key_name'        => $this->request->getPost('key_name'),
            'label'           => $this->request->getPost('label'),
            'filename_suffix' => $this->request->getPost('filename_suffix'),
            'is_active'       => (int) $this->request->getPost('is_active'),
            'sort_order'      => (int) $this->request->getPost('sort_order'),
        ];

        if ($id) {
            $this->typeModel->update($id, $data);
        } else {
            $this->typeModel->insert($data);
        }

        RecordTypeModel::clearCache();
        return redirect()->to('/super-admin/record-types')->with('success', 'Record type saved.');
    }

    // POST super-admin/record-types/delete-type/(:num)
    public function deleteType(int $id)
    {
        if ($r = $this->requireAdmin()) return $r;
        $this->typeModel->delete($id);
        RecordTypeModel::clearCache();
        return redirect()->to('/super-admin/record-types')->with('success', 'Type deleted.');
    }

    // GET  super-admin/record-types/keywords/(:num)  [AJAX]
    public function getKeywords(int $typeId)
    {
        if ($r = $this->requireAdmin()) return $r;
        $db       = \Config\Database::connect();
        $keywords = $db->table('record_keywords')
            ->where('type_id', $typeId)->get()->getResultArray();
        return $this->response->setJSON(['success' => true, 'keywords' => $keywords]);
    }

    // POST super-admin/record-types/save-keyword
    public function saveKeyword()
    {
        if ($r = $this->requireAdmin()) return $r;
        $db = \Config\Database::connect();
        $db->table('record_keywords')->insert([
            'type_id' => $this->request->getPost('type_id'),
            'keyword' => trim($this->request->getPost('keyword')),
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    // POST super-admin/record-types/delete-keyword/(:num)
    public function deleteKeyword(int $keywordId)
    {
        if ($r = $this->requireAdmin()) return $r;
        $db = \Config\Database::connect();
        $db->table('record_keywords')->delete(['keyword_id' => $keywordId]);
        return $this->response->setJSON(['success' => true]);
    }
}