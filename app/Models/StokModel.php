<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table = 'stok';
    protected $primaryKey = 'id_bag';
    protected $allowedFields = ['no_kantong', 'id_produsen', 'jenis_darah', 'gol_dar', 'rhesus', 'volume', 'tanggal_produksi', 'tanggal_expired', 'status', 'keterangan'];
    protected $useTimestamps = false;

    public function getStokWithDetails($search = '', $perPage = 10)
    {
        $builder = $this->select('stok.*, produsen.nama as nama_produsen, produsen.jenis as jenis_produsen')
                       ->join('produsen', 'produsen.id_produsen = stok.id_produsen');

        if ($search) {
            $builder->groupStart()
                   ->like('stok.no_kantong', $search)
                   ->orLike('stok.gol_dar', $search)
                   ->orLike('stok.jenis_darah', $search)
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
        return $this->select('gol_dar, COUNT(*) as total')
                   ->where('status', 'tersedia')
                   ->groupBy('gol_dar')
                   ->get()
                   ->getResultArray();
    }

    public function getStokByJenis()
    {
        return $this->select('jenis_darah, COUNT(*) as total')
                   ->where('status', 'tersedia')
                   ->groupBy('jenis_darah')
                   ->get()
                   ->getResultArray();
    }

    public function getStokByGolonganRhesus()
    {
        return $this->select('gol_dar, rhesus, COUNT(*) as total')
                   ->where('status', 'tersedia')
                   ->groupBy('gol_dar, rhesus')
                   ->orderBy('gol_dar', 'ASC')
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

    /**
     * Return distinct golongan darah values from stok table as simple array
     */
    public function getDistinctGolongan()
    {
        $rows = $this->select('gol_dar')
                     ->distinct()
                     ->where('gol_dar IS NOT NULL')
                     ->orderBy('gol_dar')
                     ->get()
                     ->getResultArray();

        return array_column($rows, 'gol_dar');
    }

    /**
     * Return distinct jenis_darah values from stok table as simple array
     */
    public function getDistinctJenis()
    {
        $rows = $this->select('jenis_darah')
                     ->distinct()
                     ->where('jenis_darah IS NOT NULL')
                     ->orderBy('jenis_darah')
                     ->get()
                     ->getResultArray();

        return array_column($rows, 'jenis_darah');
    }

    public function getDistinctGolonganByProdusen($id_produsen)
    {
        $rows = $this->select('gol_dar')
                     ->distinct()
                     ->where('id_produsen', $id_produsen)
                     ->where('status', 'tersedia')
                     ->where('gol_dar IS NOT NULL')
                     ->where('tanggal_expired >=', date('Y-m-d'))
                     ->orderBy('gol_dar')
                     ->get()
                     ->getResultArray();

        return array_column($rows, 'gol_dar');
    }

    public function getAvailableStockForProdusen($id_produsen, $gol_dar = null, $jenis = null)
    {
        $builder = $this->where('id_produsen', $id_produsen)
                        ->where('status', 'tersedia')
                        ->where('tanggal_expired >=', date('Y-m-d'));

        if ($gol_dar) {
            $builder->where('gol_dar', $gol_dar);
        }

        if ($jenis) {
            $builder->where('jenis_darah', $jenis);
        }

        return $builder->orderBy('tanggal_expired', 'ASC')->findAll();
    }

    public function countAvailableByProdusenGolonganJenis($id_produsen, $gol_dar, $jenis)
    {
        return $this->where('id_produsen', $id_produsen)
                    ->where('status', 'tersedia')
                    ->where('tanggal_expired >=', date('Y-m-d'))
                    ->where('gol_dar', $gol_dar)
                    ->where('jenis_darah', $jenis)
                    ->countAllResults();
    }
}
