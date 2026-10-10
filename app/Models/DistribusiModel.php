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
        $builder = $this->select('distribusi.*, stok.id_produsen as stok_id_produsen, stok.no_kantong, stok.gol_dar, stok.jenis_darah, rumah_sakit.nama_rs, produsen.nama as nama_produsen')
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

    public function getTotalDistribusi($rsId = null)
    {
        $builder = $this->db->table('permintaan')
                           ->where('status', 'completed');

        if ($rsId !== null) {
            $builder->where('id_rs', $rsId);
        }

        return $builder->countAllResults();
    }

    public function getDistribusiHariIni($rsId = null)
    {
        return $this->countCompletedDistribusi($rsId, 'today');
    }

    public function getDistribusiBulanIni($rsId = null)
    {
        return $this->countCompletedDistribusi($rsId, 'month');
    }

    private function countCompletedDistribusi($rsId = null, $period = null)
    {
        $builder = $this->db->table('distribusi d')
                           ->select('COUNT(DISTINCT d.id_distribusi) AS total', false)
                           ->join('permintaan p', 'p.id_rs = d.id_rs AND p.id_bag = d.id_bag')
                           ->where('p.status', 'completed');

        if ($rsId !== null) {
            $builder->where('d.id_rs', $rsId);
        }

        if ($period === 'today') {
            $builder->where('DATE(d.tanggal_distribusi)', date('Y-m-d'));
        } elseif ($period === 'month') {
            $builder->where('MONTH(d.tanggal_distribusi)', date('m'))
                    ->where('YEAR(d.tanggal_distribusi)', date('Y'));
        }

        $row = $builder->get()->getRowArray();

        return (int) ($row['total'] ?? 0);
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

    public function getRecentDistribusi($limit = 5, $rsId = null)
    {
        $builder = $this->select('distribusi.*, stok.no_kantong, stok.gol_dar, rumah_sakit.nama_rs, produsen.nama as nama_produsen')
                        ->join('stok', 'stok.id_bag = distribusi.id_bag')
                        ->join('rumah_sakit', 'rumah_sakit.id_rs = distribusi.id_rs')
                        ->join('produsen', 'produsen.id_produsen = stok.id_produsen', 'left');
        if ($rsId !== null) {
            $builder->where('distribusi.id_rs', $rsId);
        }

        return $builder->orderBy('distribusi.tanggal_distribusi', 'DESC')
                       ->limit($limit)
                       ->get()
                       ->getResultArray();
    }

    public function getStokPerBDRS($id_user = null)
    {
        // Aggregate by produsen (BDRS) so producers without linked login users are still shown
        $builder = $this->db->table('produsen p')
                   ->select('p.id_produsen, p.nama as nama_produsen,
                         COALESCE(SUM(CASE WHEN s.status = "tersedia" AND s.tanggal_expired >= CURDATE() THEN 1 ELSE 0 END), 0) as jumlah_stok,
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
