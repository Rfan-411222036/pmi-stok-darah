<?php

namespace App\Controllers;

use App\Models\ReplenishmentModel;
use App\Models\ProdusenModel;

class Replenishment extends BaseController
{
    protected $replenishmentModel;
    protected $produsenModel;

    public function __construct()
    {
        $this->replenishmentModel = new ReplenishmentModel();
        $this->produsenModel      = new ProdusenModel();
    }

    public function index()
    {
        $search  = $this->request->getGet('search') ?? '';
        $perPage = 10;

        $result = $this->replenishmentModel->getWithDetails($search, $perPage);

        return view('replenishment/index', [
            'title'      => 'Replenishment',
            'page_title' => 'Tiket Replenishment Stok',
            'tickets'    => $result['tickets'],
            'pager'      => $result['pager'],
            'search'     => $search,
        ]);
    }

    public function show($id)
    {
        $ticket = $this->replenishmentModel->getDetail($id);

        if (!$ticket) {
            session()->setFlashdata('error', 'Tiket tidak ditemukan.');
            return redirect()->to('/replenishment');
        }

        return view('replenishment/show', [
            'title'      => 'Detail Replenishment',
            'page_title' => 'Detail Tiket Replenishment',
            'ticket'     => $ticket,
        ]);
    }

    public function fulfill($id)
    {
        $ticket = $this->replenishmentModel->getDetail($id);

        if (!$ticket || $ticket['status'] !== 'pending') {
            session()->setFlashdata('error', 'Tiket tidak valid atau sudah diproses.');
            return redirect()->to('/replenishment');
        }

        $fulfilled = (int) $this->request->getPost('fulfilled_units');
        $notes     = $this->request->getPost('notes');

        if ($fulfilled <= 0) {
            session()->setFlashdata('error', 'Jumlah unit yang dipenuhi harus lebih dari 0.');
            return redirect()->back();
        }

        $this->replenishmentModel->fulfill($id, $fulfilled, $notes);

        session()->setFlashdata('success', "Tiket #{$id} berhasil dipenuhi ({$fulfilled} unit).");
        return redirect()->to('/replenishment');
    }

    public function cancel($id)
    {
        $ticket = $this->replenishmentModel->getDetail($id);

        if (!$ticket || $ticket['status'] !== 'pending') {
            session()->setFlashdata('error', 'Tiket tidak valid atau sudah diproses.');
            return redirect()->to('/replenishment');
        }

        $this->replenishmentModel->update($id, ['status' => 'cancelled']);

        session()->setFlashdata('success', "Tiket #{$id} dibatalkan.");
        return redirect()->to('/replenishment');
    }
}
