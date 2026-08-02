<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdusenModel extends Model
{
    protected $table = 'produsen';
    protected $primaryKey = 'id_produsen';
    protected $allowedFields = ['id_user', 'nama', 'jenis', 'no_kantong', 'status', 'alamat', 'telepon', 'is_active'];
    protected $useTimestamps = false;

    public function getProdusen($search = '', $perPage = 10, $userId = null)
    {
        // Check whether the `is_active` and `id_user` columns exist in the table.
        $fields = [];
        try {
            $fields = $this->db->getFieldNames($this->table);
        } catch (\Exception $e) {
            $fields = [];
        }

        $hasIdUser = in_array('id_user', $fields);

        // Use the model instance as the query builder so paginate() works.
        $builder = $this;

        if ($userId && $hasIdUser) {
            $builder = $builder->where('id_user', $userId);
        }

        if ($search) {
            $builder->groupStart()
                   ->like('nama', $search)
                   ->orLike('jenis', $search)
                   ->orLike('telepon', $search)
                   ->orLike('alamat', $search)
                   ->orLike('no_kantong', $search)
                   ->orLike('status', $search)
                   ->groupEnd();
        }

        return [
            'produsen' => $builder->paginate($perPage),
            'pager' => $builder->pager
        ];
    }

    public function getTotalProdusen($userId = null)
    {
        // Only filter by is_active when the column exists.
        try {
            $fields = $this->db->getFieldNames($this->table);
        } catch (\Exception $e) {
            $fields = [];
        }

        $builder = $this;

        if ($userId && in_array('id_user', $fields)) {
            $builder = $builder->where('id_user', $userId);
        }

        return $builder->countAllResults();
    }

    public function getProdusenByUser($userId)
    {
        if (!$userId) {
            return null;
        }

        $fields = [];
        try {
            $fields = $this->db->getFieldNames($this->table);
        } catch (\Exception $e) {
            $fields = [];
        }

        if (!in_array('id_user', $fields)) {
            return null;
        }

        return $this->where('id_user', $userId)->where('is_active', 1)->first();
    }

    // Soft delete method
    public function softDelete($id)
    {
        // If the table has `is_active`, use soft delete by toggling it; otherwise perform a hard delete.
        try {
            $fields = $this->db->getFieldNames($this->table);
        } catch (\Exception $e) {
            $fields = [];
        }

        if (in_array('is_active', $fields)) {
            return $this->update($id, ['is_active' => 0]);
        }

        return $this->delete($id);
    }
}
