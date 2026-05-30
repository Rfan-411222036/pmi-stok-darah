<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table = 'stok';
    protected $primaryKey = 'idbag';
    protected $allowedFields = ['no_kantong', 'idprodusen', 'jenisdarah', 'goldar', 'rhesus', 'volume', 'tanggal_produksi', 'tanggal_expired', 'status', 'keterangan'];
    protected $useTimestamps = false;

    public function getStokWithDetails($search = '', $perPage = 10)
    {
        $builder = $this->select('stok.*, produsen.nama as nama_produsen, produsen.jenis as jenis_produsen')
                       ->join('produsen', 'produsen.idprodusen = stok.idprodusen');

        if ($search) {
            $builder->groupStart()
                   ->like('stok.no_kantong', $search)
                   ->orLike('stok.goldar', $search)
                   ->orLike('stok.jenisdarah', $search)
                   ->orLike('produsen.nama', $search)
                   ->groupEnd();
        }

        $builder->orderBy('stok.tanggal_expired', 'ASC');

        $data = [
            'stok' => $builder->paginate($perPage),
            'pager' => $this->pager
        ];

        return $data;
    }

    public function getStokTersedia()
    {
        return $this->where('status', 'tersedia')->countAllResults();
    }

    public function getStokByGolongan()
    {
        return $this->select('goldar, COUNT(*) as total')
                   ->where('status', 'tersedia')
                   ->groupBy('goldar')
                   ->get()
                   ->getResultArray();
    }

    public function getStokByJenis()
    {
        return $this->select('jenisdarah, COUNT(*) as total')
                   ->where('status', 'tersedia')
                   ->groupBy('jenisdarah')
                   ->get()
                   ->getResultArray();
    }

    public function getStokByGolonganRhesus()
    {
        return $this->select('goldar, rhesus, COUNT(*) as total')
                   ->where('status', 'tersedia')
                   ->groupBy('goldar, rhesus')
                   ->orderBy('goldar', 'ASC')
                   ->orderBy('rhesus', 'DESC')
                   ->get()
                   ->getResultArray();
    }

    public function getStokMendekatiExpired()
    {
        $date = date('Y-m-d');
        $expiredDate = date('Y-m-d', strtotime('+7 days'));

        return $this->where('status', 'tersedia')
                   ->where('tanggal_expired >=', $date)
                   ->where('tanggal_expired <=', $expiredDate)
                   ->countAllResults();
    }

    public function getStokExpired()
    {
        return $this->where('status', 'tersedia')
                   ->where('tanggal_expired <', date('Y-m-d'))
                   ->countAllResults();
    }

    public function getStokForDistribusi()
    {
        return $this->where('status', 'tersedia')
                   ->where('tanggal_expired >=', date('Y-m-d'))
                   ->orderBy('tanggal_expired', 'ASC')
                   ->findAll();
    }
}
