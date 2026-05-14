<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdusenModel extends Model
{
    protected $table = 'produsen';
    protected $primaryKey = 'idprodusen';
    protected $allowedFields = ['iduser', 'nama', 'jenis', 'jenis_darah', 'no_kantong', 'status', 'alamat', 'is_active'];
    protected $useTimestamps = false;

    public function getProdusen($search = '', $perPage = 10)
    {
        // Check whether the `is_active` column exists in the table.
        $fields = [];
        try {
            $fields = $this->db->getFieldNames($this->table);
        } catch (\Exception $e) {
            // If getting field names fails, assume table structure is unknown and continue without is_active filter.
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
                   ->like('nama', $search)
                   ->orLike('jenis', $search)
                   ->orLike('jenis_darah', $search)
                   ->orLike('no_kantong', $search)
                   ->orLike('status', $search)
                   ->groupEnd();
        }

        return [
            'produsen' => $builder->paginate($perPage),
            'pager' => $builder->pager
        ];
    }

    public function getTotalProdusen()
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