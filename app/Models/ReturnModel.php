<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturnModel extends Model
{
    protected $table = 'return_darah';
    protected $primaryKey = 'id_return';
    protected $allowedFields = ['id_distribusi', 'id_bag', 'id_rs', 'tanggal_retur', 'alasan_return', 'kondisi_darah', 'keterangan'];
    protected $useTimestamps = false;

    public function getReturnWithDetails($search = '', $perPage = 10)
    {
        $builder = $this->select('return_darah.*, stok.no_kantong, stok.gol_dar, stok.jenis_darah, rumah_sakit.nama_rs, distribusi.tanggal_distribusi, distribusi.penerima, produsen.nama as nama_produsen')
                   ->join('stok', 'stok.id_bag = return_darah.id_bag')
                   ->join('rumah_sakit', 'rumah_sakit.id_rs = return_darah.id_rs', 'left')
                   ->join('distribusi', 'distribusi.id_distribusi = return_darah.id_distribusi')
                   ->join('produsen', 'produsen.id_produsen = stok.id_produsen', 'left');

        if ($search) {
            $builder->groupStart()
                   ->like('stok.no_kantong', $search)
                   ->orLike('rumah_sakit.nama_rs', $search)
                   ->orLike('return_darah.alasan_return', $search)
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
        return $this->select('return_darah.*, stok.no_kantong, stok.gol_dar, rumah_sakit.nama_rs, produsen.nama as nama_produsen')
               ->join('stok', 'stok.id_bag = return_darah.id_bag')
               ->join('rumah_sakit', 'rumah_sakit.id_rs = return_darah.id_rs', 'left')
               ->join('produsen', 'produsen.id_produsen = stok.id_produsen', 'left')
               ->orderBy('return_darah.tanggal_retur', 'DESC')
                   ->limit($limit)
                   ->get()
                   ->getResultArray();
    }

    public function getDistribusiForReturn()
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT d.*, s.no_kantong, s.gol_dar, s.jenis_darah, s.tanggal_expired, rs.nama_rs, p.nama as nama_produsen
            FROM distribusi d
            JOIN stok s ON s.id_bag = d.id_bag
            LEFT JOIN rumah_sakit rs ON rs.id_rs = d.id_rs
            LEFT JOIN produsen p ON p.id_produsen = s.id_produsen
            WHERE s.status = 'terdistribusi'
            AND s.tanggal_expired >= CURDATE()
            AND d.id_bag NOT IN (SELECT id_bag FROM return_darah)
            ORDER BY d.tanggal_distribusi DESC
        ")->getResultArray();
    }
}
