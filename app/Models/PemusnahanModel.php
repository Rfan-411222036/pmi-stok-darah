<?php

namespace App\Models;

use CodeIgniter\Model;

class PemusnahanModel extends Model
{
    protected $table = 'pemusnahan';
    protected $primaryKey = 'id_pemusnahan';
    protected $allowedFields = ['id_bag', 'tanggal_pemusnahan', 'alasan', 'keterangan', 'petugas'];
    protected $useTimestamps = false;

    public function getPemusnahanWithDetails($search = '', $perPage = 10)
    {
        $builder = $this->select('pemusnahan.*, stok.no_kantong, stok.gol_dar, stok.jenis_darah, stok.tanggal_expired')
                       ->join('stok', 'stok.id_bag = pemusnahan.id_bag');

        if ($search) {
            $builder->groupStart()
                   ->like('stok.no_kantong', $search)
                   ->orLike('pemusnahan.alasan', $search)
                   ->orLike('pemusnahan.petugas', $search)
                   ->groupEnd();
        }

        $builder->orderBy('pemusnahan.tanggal_pemusnahan', 'DESC');

        $data = [
            'pemusnahan' => $builder->paginate($perPage),
            'pager' => $this->pager
        ];

        return $data;
    }

    public function getTotalPemusnahan()
    {
        return $this->countAll();
    }
}
