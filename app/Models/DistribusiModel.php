<?php

namespace App\Models;

use CodeIgniter\Model;

class DistribusiModel extends Model
{
    protected $table = 'distribusi';
    protected $primaryKey = 'iddistribusi';
    protected $allowedFields = ['idbag', 'idrs', 'tanggal_distribusi', 'penerima', 'keperluan', 'no_permintaan'];
    protected $useTimestamps = false;

    public function getDistribusiWithDetails($search = '', $perPage = 10)
    {
        $builder = $this->select('distribusi.*, stok.no_kantong, stok.goldar, stok.jenisdarah, rumah_sakit.nama_rs')
                       ->join('stok', 'stok.idbag = distribusi.idbag')
                       ->join('rumah_sakit', 'rumah_sakit.idrs = distribusi.idrs');

        if ($search) {
            $builder->groupStart()
                   ->like('stok.no_kantong', $search)
                   ->orLike('rumah_sakit.nama_rs', $search)
                   ->orLike('distribusi.penerima', $search)
                   ->orLike('distribusi.no_permintaan', $search)
                   ->groupEnd();
        }

        $builder->orderBy('distribusi.tanggal_distribusi', 'DESC');

        return [
            'distribusi' => $builder->paginate($perPage),
            'pager' => $this->pager
        ];
    }

    public function getTotalDistribusi()
    {
        return $this->countAll();
    }

    public function getDistribusiHariIni()
    {
        return $this->where('DATE(tanggal_distribusi)', date('Y-m-d'))->countAllResults();
    }

    public function getDistribusiBulanIni()
    {
        return $this->where('MONTH(tanggal_distribusi)', date('m'))
                   ->where('YEAR(tanggal_distribusi)', date('Y'))
                   ->countAllResults();
    }

    public function getRecentDistribusi($limit = 5)
    {
        return $this->select('distribusi.*, stok.no_kantong, stok.goldar, rumah_sakit.nama_rs')
                   ->join('stok', 'stok.idbag = distribusi.idbag')
                   ->join('rumah_sakit', 'rumah_sakit.idrs = distribusi.idrs')
                   ->orderBy('distribusi.tanggal_distribusi', 'DESC')
                   ->limit($limit)
                   ->get()
                   ->getResultArray();
    }

    public function getStokPerBDRS($iduser = null)
    {
        $builder = $this->db->table('login l')
                           ->select('l.nama,
                                     COUNT(s.no_kantong) as jumlah_stok,
                                     SUM(CASE WHEN s.tanggal_expired >= CURDATE() AND s.status = "tersedia" THEN 1 ELSE 0 END) as layak_pakai,
                                     SUM(CASE WHEN s.tanggal_expired < CURDATE() OR s.status = "expired" THEN 1 ELSE 0 END) as sudah_expired')
                           ->join('produsen p', 'l.iduser = p.iduser', 'left')
                           ->join('stok s', 'p.idprodusen = s.idprodusen', 'left')
                           ->where('l.role', 'bdrs');

        if ($iduser) {
            $builder->where('l.iduser', $iduser);
        }

        $builder->groupBy('l.iduser, l.nama')
                ->orderBy('l.nama');

        return $builder->get()->getResultArray();
    }
}
