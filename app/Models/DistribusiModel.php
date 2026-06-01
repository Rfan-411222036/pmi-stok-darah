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

    /**
     * Get monthly distribution totals for the last N months (including months with 0).
     * Returns array of ['label', 'year', 'month', 'total'].
     */
    public function getMonthlyDistribusi(int $months = 12): array
    {
        $query = $this->db->query("
            SELECT
                YEAR(tanggal_distribusi)  AS `year`,
                MONTH(tanggal_distribusi) AS `month`,
                COUNT(*)                 AS total
            FROM distribusi
            WHERE tanggal_distribusi >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL ? MONTH), '%Y-%m-01')
            GROUP BY YEAR(tanggal_distribusi), MONTH(tanggal_distribusi)
        ", [$months]);

        $rows = $query->getResultArray();

        // Index fetched rows by 'YYYY-MM' key
        $map = [];
        foreach ($rows as $r) {
            $key = $r['year'] . '-' . str_pad($r['month'], 2, '0', STR_PAD_LEFT);
            $map[$key] = (int) $r['total'];
        }

        // Fill all N months, oldest first
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $ts    = strtotime("-{$i} months");
            $y     = (int) date('Y', $ts);
            $m     = (int) date('n', $ts);
            $key   = $y . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
            $result[] = [
                'label' => date('M Y', $ts),
                'year'  => $y,
                'month' => $m,
                'total' => $map[$key] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Get per-BDRS distribution count for a specific year/month.
     * Returns array of ['bdrs_nama', 'total'].
     */
    public function getMonthlyDistribusiPerBDRS(int $year, int $month): array
    {
        $query = $this->db->query("
            SELECT
                p.nama   AS bdrs_nama,
                COUNT(*) AS total
            FROM distribusi d
            JOIN stok s     ON s.id_bag      = d.id_bag
            JOIN produsen p ON p.id_produsen = s.id_produsen
            WHERE YEAR(d.tanggal_distribusi)  = ?
              AND MONTH(d.tanggal_distribusi) = ?
            GROUP BY p.id_produsen, p.nama
            ORDER BY total DESC
        ", [$year, $month]);

        return $query->getResultArray();
    }
}
