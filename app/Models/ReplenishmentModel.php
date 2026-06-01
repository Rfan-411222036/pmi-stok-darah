<?php

namespace App\Models;

use CodeIgniter\Model;

class ReplenishmentModel extends Model
{
    protected $table      = 'replenishment_tickets';
    protected $primaryKey = 'id_replenishment';
    protected $allowedFields = [
        'id_produsen', 'gol_dar', 'rhesus', 'jenis_darah',
        'requested_units', 'fulfilled_units', 'status', 'notes',
        'requested_at', 'fulfilled_at',
    ];
    protected $useTimestamps = false;

    public function getWithDetails($search = '', $perPage = 10)
    {
        $builder = $this->select('replenishment_tickets.*, produsen.nama as nama_produsen')
                        ->join('produsen', 'produsen.id_produsen = replenishment_tickets.id_produsen');

        if ($search) {
            $builder->groupStart()
                    ->like('produsen.nama', $search)
                    ->orLike('replenishment_tickets.gol_dar', $search)
                    ->orLike('replenishment_tickets.status', $search)
                    ->groupEnd();
        }

        $builder->orderBy('replenishment_tickets.requested_at', 'DESC');

        return [
            'tickets' => $builder->paginate($perPage),
            'pager'   => $this->pager,
        ];
    }

    public function getDetail($id)
    {
        return $this->select('replenishment_tickets.*, produsen.nama as nama_produsen, produsen.alamat as alamat_produsen')
                    ->join('produsen', 'produsen.id_produsen = replenishment_tickets.id_produsen')
                    ->where('replenishment_tickets.id_replenishment', $id)
                    ->first();
    }

    /**
     * Check if a pending ticket already exists for a BDRS + blood type combo.
     * Prevents duplicate tickets from the same audit run.
     */
    public function pendingExists($id_produsen, $gol_dar, $rhesus, $jenis_darah)
    {
        return $this->where('id_produsen', $id_produsen)
                    ->where('gol_dar', $gol_dar)
                    ->where('rhesus', $rhesus)
                    ->where('jenis_darah', $jenis_darah)
                    ->where('status', 'pending')
                    ->countAllResults() > 0;
    }

    public function fulfill($id, $fulfilled_units, $notes = null)
    {
        return $this->update($id, [
            'fulfilled_units' => $fulfilled_units,
            'status'          => 'fulfilled',
            'fulfilled_at'    => date('Y-m-d H:i:s'),
            'notes'           => $notes,
        ]);
    }
}
