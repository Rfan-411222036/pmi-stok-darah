<?php

namespace App\Models;

use CodeIgniter\Model;

class DistribusiModel extends Model
{
    protected $table = 'distribusi';
    protected $primaryKey = 'id_distribusi';
    protected $allowedFields = ['id_bag', 'id_rs', 'tanggal_distribusi', 'penerima', 'keperluan', 'no_permintaan'];
    protected $useTimestamps = false;

    public function getDistribusiWithDetails($search = '', $perPage = 10)
    {
        $builder = $this->select('distribusi.*, stok.no_kantong, stok.gol_dar, stok.jenis_darah, rumah_sakit.nama_rs')
                       ->join('stok', 'stok.id_bag = distribusi.id_bag')
                       ->join('rumah_sakit', 'rumah_sakit.id_rs = distribusi.id_rs');

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
        return $this->select('distribusi.*, stok.no_kantong, stok.gol_dar, rumah_sakit.nama_rs')
                   ->join('stok', 'stok.id_bag = distribusi.id_bag')
                   ->join('rumah_sakit', 'rumah_sakit.id_rs = distribusi.id_rs')
                   ->orderBy('distribusi.tanggal_distribusi', 'DESC')
                   ->limit($limit)
                   ->get()
                   ->getResultArray();
    }

    public function getStokPerBDRS($id_user = null)
    {
        $builder = $this->db->table('login l')
                           ->select('l.nama,
                                     COUNT(s.no_kantong) as jumlah_stok,
                                     SUM(CASE WHEN s.tanggal_expired >= CURDATE() AND s.status = "tersedia" THEN 1 ELSE 0 END) as layak_pakai,
                                     SUM(CASE WHEN s.tanggal_expired < CURDATE() OR s.status = "expired" THEN 1 ELSE 0 END) as sudah_expired')
                           ->join('produsen p', 'l.id_user = p.id_user', 'left')
                           ->join('stok s', 'p.id_produsen = s.id_produsen', 'left')
                           ->where('l.role', 'bdrs');

        if ($id_user) {
            $builder->where('l.id_user', $id_user);
        }

        $builder->groupBy('l.id_user, l.nama')
                ->orderBy('l.nama');

        return $builder->get()->getResultArray();
    }
}
