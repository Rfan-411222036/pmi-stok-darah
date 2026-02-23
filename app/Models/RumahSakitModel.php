<?php

namespace App\Models;

use CodeIgniter\Model;

class RumahSakitModel extends Model
{
    protected $table = 'rumah_sakit';
    protected $primaryKey = 'idrs';
    protected $allowedFields = ['nama_rs', 'alamat', 'telepon', 'email', 'jenis_rs', 'is_active'];
    protected $useTimestamps = false;

    public function getRumahSakit($search = '', $perPage = 10)
    {
        // Check whether the `is_active` column exists in the table.
        $fields = [];
        try {
            $fields = $this->db->getFieldNames($this->table);
        } catch (\Exception $e) {
            $fields = [];
        }

        $hasIsActive = in_array('is_active', $fields);

        // Use the model instance as the query builder so paginate() works.
        $builder = $this;

        if ($hasIsActive) {
            $builder = $builder->where('is_active', 1);
        }

        if ($search) {
            $builder->groupStart()
                   ->like('nama_rs', $search)
                   ->orLike('jenis_rs', $search)
                   ->orLike('telepon', $search)
                   ->groupEnd();
        }

        return [
            'rumah_sakit' => $builder->paginate($perPage),
            'pager' => $builder->pager
        ];
    }

    public function getTotalRumahSakit()
    {
        // Only filter by is_active when the column exists.
        try {
            $fields = $this->db->getFieldNames($this->table);
        } catch (\Exception $e) {
            $fields = [];
        }

        if (in_array('is_active', $fields)) {
            return $this->where('is_active', 1)->countAllResults();
        }

        return $this->countAllResults();
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