<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturnModel extends Model
{
    protected $table = 'return_darah';
    protected $primaryKey = 'idreturn';
    protected $allowedFields = ['iddistribusi', 'idbag', 'idrs', 'tanggal_retur', 'alasan_return', 'kondisi_darah', 'ditangani_oleh', 'keterangan'];
    protected $useTimestamps = false;

    public function getReturnWithDetails($search = '', $perPage = 10)
    {
        $builder = $this->select('return_darah.*, stok.no_kantong, stok.goldar, stok.jenisdarah, rumah_sakit.nama_rs, distribusi.tanggal_distribusi, distribusi.penerima')
                       ->join('stok', 'stok.idbag = return_darah.idbag')
                       ->join('rumah_sakit', 'rumah_sakit.idrs = return_darah.idrs')
                       ->join('distribusi', 'distribusi.iddistribusi = return_darah.iddistribusi');
        
        if ($search) {
            $builder->groupStart()
                   ->like('stok.no_kantong', $search)
                   ->orLike('rumah_sakit.nama_rs', $search)
                   ->orLike('return_darah.alasan_return', $search)
                   ->orLike('return_darah.ditangani_oleh', $search)
                   ->groupEnd();
        }

        $builder->orderBy('return_darah.tanggal_retur', 'DESC');
        
        $data = [
            'return' => $builder->paginate($perPage),
            'pager' => $this->pager
        ];

        return $data;
    }

    public function getTotalReturn()
    {
        return $this->countAll();
    }

    public function getReturnHariIni()
    {
        return $this->where('DATE(tanggal_retur)', date('Y-m-d'))->countAllResults();
    }

    public function getReturnBulanIni()
    {
        return $this->where('MONTH(tanggal_retur)', date('m'))
               ->where('YEAR(tanggal_retur)', date('Y'))
                   ->countAllResults();
    }

    public function getRecentReturn($limit = 5)
    {
        return $this->select('return_darah.*, stok.no_kantong, stok.goldar, rumah_sakit.nama_rs')
               ->join('stok', 'stok.idbag = return_darah.idbag')
               ->join('rumah_sakit', 'rumah_sakit.idrs = return_darah.idrs')
               ->orderBy('return_darah.tanggal_retur', 'DESC')
                   ->limit($limit)
                   ->get()
                   ->getResultArray();
    }

    public function getDistribusiForReturn()
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT d.*, s.no_kantong, s.goldar, s.jenisdarah, s.tanggal_expired, rs.nama_rs
            FROM distribusi d
            JOIN stok s ON s.idbag = d.idbag
            JOIN rumah_sakit rs ON rs.idrs = d.idrs
            WHERE s.status = 'terdistribusi'
            AND s.tanggal_expired >= CURDATE()
            AND d.idbag NOT IN (SELECT idbag FROM return_darah)
            ORDER BY d.tanggal_distribusi DESC
        ")->getResultArray();
    }
}