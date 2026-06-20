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

    public function getForProdusen($id_produsen, array $filters = [])
    {
        $filters['id_produsen'] = $id_produsen;
        return $this->filterQuery($this->baseQuery(), $filters)->findAll();
    }

    public function getByCreator($userId, array $filters = [])
    {
        $filters['created_by'] = $userId;
        return $this->filterQuery($this->baseQuery(), $filters)->findAll();
    }

    public function getAllWithNames(array $filters = [])
    {
        return $this->filterQuery($this->baseQuery(), $filters)->findAll();
    }

    protected function baseQuery()
    {
        return $this->select('permintaan.*, rumah_sakit.nama_rs, produsen.nama as nama_produsen, stok.no_kantong')
                    ->join('rumah_sakit', 'rumah_sakit.id_rs = permintaan.id_rs', 'left')
                    ->join('produsen', 'produsen.id_produsen = permintaan.id_produsen', 'left')
                    ->join('stok', 'stok.id_bag = permintaan.id_bag', 'left');
    }

    protected function filterQuery($builder, array $filters)
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                    ->like('rumah_sakit.nama_rs', $search)
                    ->orLike('produsen.nama', $search)
                    ->orLike('permintaan.keterangan', $search)
                    ->orLike('permintaan.nama_penerima', $search)
                    ->orLike('stok.no_kantong', $search)
                    ->groupEnd();
        }

        if (!empty($filters['from'])) {
            $builder->where('permintaan.created_at >=', $filters['from'] . ' 00:00:00');
        }
        if (!empty($filters['to'])) {
            $builder->where('permintaan.created_at <=', $filters['to'] . ' 23:59:59');
        }

        if (!empty($filters['keperluan'])) {
            $builder->where('permintaan.keterangan', $filters['keperluan']);
        }

        if (!empty($filters['gol_dar'])) {
            $builder->where('permintaan.gol_dar', $filters['gol_dar']);
        }

        if (!empty($filters['jenis'])) {
            $builder->where('permintaan.jenis', $filters['jenis']);
        }

        if (!empty($filters['id_produsen'])) {
            $builder->where('permintaan.id_produsen', $filters['id_produsen']);
        }

        if (!empty($filters['id_rs'])) {
            $builder->where('permintaan.id_rs', $filters['id_rs']);
        }

        if (!empty($filters['created_by'])) {
            $builder->where('permintaan.created_by', $filters['created_by']);
        }

        if (!empty($filters['status'])) {
            $builder->where('permintaan.status', $filters['status']);
        }

        return $builder->orderBy('permintaan.created_at', 'DESC');
    }

    public function getDistinctKeterangan()
    {
        return $this->select('keterangan')
                    ->distinct()
                    ->where('keterangan IS NOT NULL', null, false)
                    ->where('keterangan !=', '')
                    ->orderBy('keterangan', 'ASC')
                    ->findAll();
    }

    public function getByRs($id_rs, array $filters = [])
    {
        $filters['id_rs'] = $id_rs;
        return $this->filterQuery($this->baseQuery(), $filters)->findAll();
    }
}
