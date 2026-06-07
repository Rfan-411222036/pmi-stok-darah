<?php

namespace App\Models;

use CodeIgniter\Model;

class PermintaanModel extends Model
{
    protected $table = 'permintaan';
    protected $primaryKey = 'id_permintaan';
    protected $allowedFields = ['id_rs', 'id_produsen', 'jumlah', 'gol_dar', 'jenis', 'keterangan', 'status', 'created_by', 'approved_by', 'approved_at', 'created_at'];
    protected $useTimestamps = false;

    public function getPendingForProdusen($id_produsen)
    {
        return $this->where('id_produsen', $id_produsen)
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getByRs($id_rs)
    {
        return $this->where('id_rs', $id_rs)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
