<?php

namespace App\Controllers;

use App\Models\PermintaanModel;
use App\Models\ProdusenModel;
use App\Models\RumahSakitModel;

class Permintaan extends BaseController
{
    protected $permintaanModel;
    protected $produsenModel;
    protected $rsModel;

    public function __construct()
    {
        $this->permintaanModel = new PermintaanModel();
        $this->produsenModel = new ProdusenModel();
        $this->rsModel = new RumahSakitModel();
    }

    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id_user');

        if ($role === 'bdrs') {
            // find produsen for this user
            $produsen = $this->produsenModel->where('id_user', $userId)->first();
            $dataList = $produsen ? $this->permintaanModel->getPendingForProdusen($produsen['id_produsen']) : [];
        } else if ($role === 'rs') {
            // if RS, show their requests (requires link between user and rs)
            $rs = $this->rsModel->where('id_user', $userId)->first();
            $dataList = $rs ? $this->permintaanModel->getByRs($rs['id_rs']) : [];
        } else {
            $dataList = $this->permintaanModel->orderBy('created_at', 'DESC')->findAll();
        }

        return view('permintaan/index', [
            'title' => 'Permintaan Darah',
            'page_title' => 'Permintaan Darah',
            'list' => $dataList
        ]);
    }

    public function create()
    {
        $produsen = $this->produsenModel->findAll();
        $rumahSakit = $this->rsModel->findAll();

        return view('permintaan/create', [
            'title' => 'Buat Permintaan',
            'page_title' => 'Buat Permintaan Darah',
            'produsen' => $produsen,
            'rumah_sakit' => $rumahSakit,
            'validation' =>
                \Config\Services::validation()
        ]);
    }

    public function store()
    {
        $post = $this->request->getPost();
        $userId = session()->get('id_user');

        $data = [
            'id_rs' => $post['id_rs'] ?? null,
            'id_produsen' => $post['id_produsen'] ?? null,
            'jumlah' => (int) ($post['jumlah'] ?? 0),
            'gol_dar' => $post['gol_dar'] ?? null,
            'jenis' => $post['jenis'] ?? null,
            'keterangan' => $post['keterangan'] ?? null,
            'status' => 'pending',
            'created_by' => $userId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->permintaanModel->insert($data);
        session()->setFlashdata('success', 'Permintaan berhasil diajukan. Menunggu persetujuan BDRS.');
        return redirect()->to('/permintaan');
    }

    public function approve($id)
    {
        $role = session()->get('role');
        $userId = session()->get('id_user');

        if ($role !== 'bdrs') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $perm = $this->permintaanModel->find($id);
        if (!$perm) return redirect()->back()->with('error', 'Permintaan tidak ditemukan');

        $this->permintaanModel->update($id, [
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Permintaan disetujui');
        return redirect()->to('/permintaan');
    }

    public function reject($id)
    {
        $role = session()->get('role');
        $userId = session()->get('id_user');

        if ($role !== 'bdrs') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $perm = $this->permintaanModel->find($id);
        if (!$perm) return redirect()->back()->with('error', 'Permintaan tidak ditemukan');

        $this->permintaanModel->update($id, [
            'status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Permintaan ditolak');
        return redirect()->to('/permintaan');
    }
}
