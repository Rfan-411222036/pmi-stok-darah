<?php

namespace App\Models;

use CodeIgniter\Model;

class RecallModel extends Model
{
    protected $table      = 'recall_tickets';
    protected $primaryKey = 'id_recall';
    protected $allowedFields = [
        'id_bag', 'id_produsen', 'reason', 'status',
        'flagged_at', 'swapped_at', 'notes',
    ];
    protected $useTimestamps = false;

    public function getWithDetails($search = '', $perPage = 10)
    {
        $builder = $this->select(
                'recall_tickets.*,
                 stok.no_kantong, stok.gol_dar, stok.rhesus, stok.jenis_darah,
                 stok.tanggal_expired,
                 produsen.nama as nama_produsen'
            )
            ->join('stok', 'stok.id_bag = recall_tickets.id_bag')
            ->join('produsen', 'produsen.id_produsen = recall_tickets.id_produsen');

        if ($search) {
            $builder->groupStart()
                    ->like('stok.no_kantong', $search)
                    ->orLike('stok.gol_dar', $search)
                    ->orLike('produsen.nama', $search)
                    ->orLike('recall_tickets.status', $search)
                    ->groupEnd();
        }

        $builder->orderBy('recall_tickets.flagged_at', 'DESC');

        return [
            'tickets' => $builder->paginate($perPage),
            'pager'   => $this->pager,
        ];
    }

    public function getDetail($id)
    {
        return $this->select(
                'recall_tickets.*,
                 stok.no_kantong, stok.gol_dar, stok.rhesus, stok.jenis_darah,
                 stok.volume, stok.tanggal_produksi, stok.tanggal_expired,
                 produsen.nama as nama_produsen'
            )
            ->join('stok', 'stok.id_bag = recall_tickets.id_bag')
            ->join('produsen', 'produsen.id_produsen = recall_tickets.id_produsen')
            ->where('recall_tickets.id_recall', $id)
            ->first();
    }

    /**
     * Check if a pending recall already exists for a given blood bag.
     * Prevents duplicate recall tickets for the same unit.
     */
    public function pendingExistsForBag($id_bag)
    {
        return $this->where('id_bag', $id_bag)
                    ->whereIn('status', ['pending'])
                    ->countAllResults() > 0;
    }

    public function markSwapped($id, $notes = null)
    {
        return $this->update($id, [
            'status'     => 'swapped',
            'swapped_at' => date('Y-m-d H:i:s'),
            'notes'      => $notes,
        ]);
    }

    public function markDestroyed($id, $notes = null)
    {
        return $this->update($id, [
            'status' => 'destroyed',
            'notes'  => $notes,
        ]);
    }
}
