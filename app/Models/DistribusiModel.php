<?php

namespace App\Models;

use CodeIgniter\Model;

class DistribusiModel extends Model
{
    protected $table = 'distribusi';
    protected $primaryKey = 'id_distribusi';
    protected $allowedFields = ['id_bag', 'id_rs', 'id_produsen', 'tanggal_distribusi', 'penerima', 'keperluan', 'no_permintaan'];
    protected $useTimestamps = false;

    public function getDistribusiWithDetails($search = '', $perPage = 10, $produsenId = null, $rsId = null)
    {
        $builder = $this->select('distribusi.*, stok.no_kantong, stok.gol_dar, stok.jenis_darah, rumah_sakit.nama_rs, produsen.nama as nama_produsen')
                       ->join('stok', 'stok.id_bag = distribusi.id_bag')
                       ->join('rumah_sakit', 'rumah_sakit.id_rs = distribusi.id_rs')
                       ->join('produsen', 'produsen.id_produsen = stok.id_produsen', 'left');

        if ($produsenId) {
            $builder = $builder->where('stok.id_produsen', $produsenId);
        }

        if ($rsId) {
            $builder = $builder->where('distribusi.id_rs', $rsId);
        }

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

    /**
     * Return distribution counts per month for the given year.
     * Returns array of 12 integers indexed 1..12
     */
    public function getDistribusiPerMonth($year = null)
    {
        $year = $year ?? date('Y');

        $db = $this->db;
        $sql = "SELECT MONTH(tanggal_distribusi) AS m, COUNT(*) AS cnt
                FROM distribusi
                WHERE YEAR(tanggal_distribusi) = ?
                GROUP BY MONTH(tanggal_distribusi)";

        $rows = $db->query($sql, [$year])->getResultArray();
        $result = array_fill(1, 12, 0);
        foreach ($rows as $r) {
            $result[(int)$r['m']] = (int)$r['cnt'];
        }

        return $result;
    }

    /**
     * Return distribution counts per produsen (BDRS) for a specific month/year.
     * Returns array of ['nama_produsen' => ..., 'count' => ...]
     */
    public function getDistribusiPerGudangForMonth($month, $year = null)
    {
        $year = $year ?? date('Y');

        $builder = $this->select('p.nama as nama_produsen, COUNT(d.id_distribusi) as jumlah')
                        ->from('distribusi d')
                        ->join('stok s', 's.id_bag = d.id_bag')
                        ->join('produsen p', 'p.id_produsen = s.id_produsen', 'left')
                        ->where('MONTH(d.tanggal_distribusi)', (int)$month)
                        ->where('YEAR(d.tanggal_distribusi)', (int)$year)
                        ->groupBy('p.id_produsen, p.nama')
                        ->orderBy('jumlah', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function getRecentDistribusi($limit = 5)
    {
        return $this->select('distribusi.*, stok.no_kantong, stok.gol_dar, rumah_sakit.nama_rs, produsen.nama as nama_produsen')
                   ->join('stok', 'stok.id_bag = distribusi.id_bag')
                   ->join('rumah_sakit', 'rumah_sakit.id_rs = distribusi.id_rs')
                   ->join('produsen', 'produsen.id_produsen = stok.id_produsen', 'left')
                   ->orderBy('distribusi.tanggal_distribusi', 'DESC')
                   ->limit($limit)
                   ->get()
                   ->getResultArray();
    }

    public function getStokPerBDRS($id_user = null)
    {
        // Aggregate by produsen (BDRS) so producers without linked login users are still shown
        $builder = $this->db->table('produsen p')
                   ->select('p.id_produsen, p.nama as nama_produsen,
                         COALESCE(SUM(CASE WHEN s.status = "tersedia" THEN 1 ELSE 0 END), 0) as jumlah_stok,
                         COALESCE(SUM(CASE WHEN s.status = "tersedia" AND s.tanggal_expired >= CURDATE() THEN 1 ELSE 0 END), 0) as layak_pakai,
                         COALESCE(SUM(CASE WHEN (s.status = "tersedia" AND s.tanggal_expired < CURDATE()) OR s.status = "expired" THEN 1 ELSE 0 END), 0) as sudah_expired')
                   ->join('stok s', 'p.id_produsen = s.id_produsen', 'left');

        if ($id_user) {
            $builder->where('p.id_user', $id_user);
        }

        $builder->groupBy('p.id_produsen, p.nama')
                ->orderBy('p.nama');

        return $builder->get()->getResultArray();
    }
}
