<?php

namespace App\Controllers;

use App\Models\RecallModel;
use App\Models\StokModel;

class Recall extends BaseController
{
    protected $recallModel;
    protected $stokModel;

    public function __construct()
    {
        $this->recallModel = new RecallModel();
        $this->stokModel   = new StokModel();
    }

    public function index()
    {
        $search  = $this->request->getGet('search') ?? '';
        $perPage = 10;

        $result = $this->recallModel->getWithDetails($search, $perPage);

        return view('recall/index', [
            'title'      => 'Recall Stok',
            'page_title' => 'Tiket Recall & Swap Stok',
            'tickets'    => $result['tickets'],
            'pager'      => $result['pager'],
            'search'     => $search,
        ]);
    }

    public function show($id)
    {
        $ticket = $this->recallModel->getDetail($id);

        if (!$ticket) {
            session()->setFlashdata('error', 'Tiket tidak ditemukan.');
            return redirect()->to('/recall');
        }

        return view('recall/show', [
            'title'      => 'Detail Recall',
            'page_title' => 'Detail Tiket Recall',
            'ticket'     => $ticket,
        ]);
    }

    public function swap($id)
    {
        $ticket = $this->recallModel->getDetail($id);

        if (!$ticket || $ticket['status'] !== 'pending') {
            session()->setFlashdata('error', 'Tiket tidak valid atau sudah diproses.');
            return redirect()->to('/recall');
        }

        $notes = $this->request->getPost('notes');

        // Mark old bag as recalled (status: dimusnahkan or recalled — use 'dimusnahkan')
        $this->stokModel->update($ticket['id_bag'], ['status' => 'dimusnahkan']);

        $this->recallModel->markSwapped($id, $notes);

        session()->setFlashdata('success', "Recall #{$id} berhasil — kantong {$ticket['no_kantong']} telah di-swap.");
        return redirect()->to('/recall');
    }

    public function destroy($id)
    {
        $ticket = $this->recallModel->getDetail($id);

        if (!$ticket || $ticket['status'] !== 'pending') {
            session()->setFlashdata('error', 'Tiket tidak valid atau sudah diproses.');
            return redirect()->to('/recall');
        }

        $notes = $this->request->getPost('notes');

        $this->stokModel->update($ticket['id_bag'], ['status' => 'dimusnahkan']);

        $this->recallModel->markDestroyed($id, $notes);

        session()->setFlashdata('success', "Kantong {$ticket['no_kantong']} telah dimusnahkan.");
        return redirect()->to('/recall');
    }
}
