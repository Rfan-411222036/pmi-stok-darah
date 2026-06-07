<?php

namespace App\Models;

use CodeIgniter\Model;

class PermintaanModel extends Model
{
    protected $table = 'permintaan';
    protected $primaryKey = 'id_permintaan';
    protected $allowedFields = ['id_rs', 'id_produsen', 'jumlah', 'gol_dar', 'jenis', 'keterangan', 'nama_penerima', 'id_bag', 'approval_note', 'status', 'created_by', 'approved_by', 'approved_at', 'created_at'];
    protected $useTimestamps = false;

    public function getPendingForProdusen($id_produsen)
    {
        return $this->select('permintaan.*, rumah_sakit.nama_rs, produsen.nama as nama_produsen, stok.no_kantong')
                    ->join('rumah_sakit', 'rumah_sakit.id_rs = permintaan.id_rs', 'left')
                    ->join('produsen', 'produsen.id_produsen = permintaan.id_produsen', 'left')
                    ->join('stok', 'stok.id_bag = permintaan.id_bag', 'left')
                    ->where('permintaan.id_produsen', $id_produsen)
                    ->where('permintaan.status', 'pending')
                    ->orderBy('permintaan.created_at', 'DESC')
                    ->findAll();
    }

    public function getForProdusen($id_produsen)
    {
        return $this->select('permintaan.*, rumah_sakit.nama_rs, produsen.nama as nama_produsen, stok.no_kantong')
                    ->join('rumah_sakit', 'rumah_sakit.id_rs = permintaan.id_rs', 'left')
                    ->join('produsen', 'produsen.id_produsen = permintaan.id_produsen', 'left')
                    ->join('stok', 'stok.id_bag = permintaan.id_bag', 'left')
                    ->where('permintaan.id_produsen', $id_produsen)
                    ->orderBy('permintaan.created_at', 'DESC')
                    ->findAll();
    }

    public function getByCreator($userId)
    {
        return $this->select('permintaan.*, rumah_sakit.nama_rs, produsen.nama as nama_produsen, stok.no_kantong')
                    ->join('rumah_sakit', 'rumah_sakit.id_rs = permintaan.id_rs', 'left')
                    ->join('produsen', 'produsen.id_produsen = permintaan.id_produsen', 'left')
                    ->join('stok', 'stok.id_bag = permintaan.id_bag', 'left')
                    ->where('permintaan.created_by', $userId)
                    ->orderBy('permintaan.created_at', 'DESC')
                    ->findAll();
    }

    public function getAllWithNames()
    {
        return $this->select('permintaan.*, rumah_sakit.nama_rs, produsen.nama as nama_produsen, stok.no_kantong')
                    ->join('rumah_sakit', 'rumah_sakit.id_rs = permintaan.id_rs', 'left')
                    ->join('produsen', 'produsen.id_produsen = permintaan.id_produsen', 'left')
                    ->join('stok', 'stok.id_bag = permintaan.id_bag', 'left')
                    ->orderBy('permintaan.created_at', 'DESC')
                    ->findAll();
    }
}
