<?php

namespace App\Controllers;

use App\Models\DistribusiModel;
use App\Models\StokModel;
use App\Models\RumahSakitModel;
use App\Models\ProdusenModel;

class Distribusi extends BaseController
{
    protected $distribusiModel;
    protected $stokModel;
    protected $rumahSakitModel;
    protected $produsenModel;

    public function __construct()
    {
        $this->distribusiModel = new DistribusiModel();
        $this->stokModel = new StokModel();
        $this->rumahSakitModel = new RumahSakitModel();
        $this->produsenModel = new ProdusenModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $perPage = 10;

        $role = session()->get('role');
        $userId = session()->get('id_user');
        $produsenFilter = null;
        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser($userId);
            $produsenFilter = $ownProdusen['id_produsen'] ?? null;
        }

        $rsFilter = null;
        if ($role === 'rs') {
            $ownRs = $this->rumahSakitModel->getRumahSakitByUser($userId);
            if (!$ownRs && session()->has('id_rs')) {
                $ownRs = $this->rumahSakitModel->find(session()->get('id_rs'));
            }
            // If still null, fall back to raw session value (some installs map only session id)
            if (!$ownRs && session()->has('id_rs')) {
                $rsFilter = session()->get('id_rs');
            }
            $rsFilter = $ownRs['id_rs'] ?? $rsFilter ?? null;
            // Final fallback: try matching by session nama -> rumah_sakit.nama_rs
            if (empty($rsFilter) && session()->has('nama')) {
                $name = session()->get('nama');
                $rsByName = $this->rumahSakitModel->where('nama_rs', $name)->first();
                if ($rsByName) {
                    $rsFilter = $rsByName['id_rs'];
                }
            }
        }

        $result = $this->distribusiModel->getDistribusiWithDetails($search, $perPage, $produsenFilter, $rsFilter);

        // provide list of produsen for request form
        if ($role === 'bdrs' && isset($ownProdusen) && $ownProdusen) {
            $produsenList = [$ownProdusen];
        } else {
            $produsenList = $this->produsenModel->orderBy('nama')->findAll();
        }

        // provide list of RS for request form fallback
        $rumahSakitList = $this->rumahSakitModel->orderBy('nama_rs')->findAll();

        // provide golongan & jenis lists from stok
        $golonganList = $this->stokModel->getDistinctGolongan();
        $jenisList = $this->stokModel->getDistinctJenis();

        // try to detect RS id for current user (if linked). Some installations don't have `id_user` in `rumah_sakit`.
        $rs = null;
        try {
            $fields = $this->rumahSakitModel->db->getFieldNames($this->rumahSakitModel->table);
        } catch (\Exception $e) {
            $fields = [];
        }

        if (in_array('id_user', $fields) && session()->has('id_user')) {
            $rs = $this->rumahSakitModel->where('id_user', session()->get('id_user'))->first();
        }

        $data = [
            'title' => 'Management Distribusi',
            'page_title' => 'Data Distribusi Darah',
            'distribusi' => $result['distribusi'],
            'pager' => $result['pager'],
            'search' => $search,
            'produsen_list' => $produsenList,
            'current_rs' => $rs,
            'own_rs_id' => $rsFilter ?? null,
            'rumah_sakit' => $rumahSakitList
            ,'golongan_list' => $golonganList
            ,'jenis_list' => $jenisList
        ];

        return view('distribusi/index', $data);
    }

    public function create()
    {
        $role = session()->get('role');
        $userId = session()->get('id_user');
        $produsenFilter = null;
        $produsenList = [];

        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser($userId);
            if ($ownProdusen) {
                $produsenFilter = $ownProdusen['id_produsen'];
                $produsenList = [$ownProdusen];
            }
        } else {
            $produsenList = $this->produsenModel->orderBy('nama')->findAll();
        }

        $stok = $this->stokModel->getStokForDistribusi($produsenFilter);
        $rumahSakit = $this->rumahSakitModel->findAll();

        $data = [
            'title' => 'Tambah Distribusi',
            'page_title' => 'Tambah Distribusi Darah',
            'stok' => $stok,
            'rumah_sakit' => $rumahSakit,
            'produsen_list' => $produsenList,
            'current_rs' => null,
            'validation' => \Config\Services::validation()
        ];
        return view('distribusi/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_bag' => 'required',
            'id_rs' => 'required',
            'penerima' => 'required',
            'tanggal_distribusi' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $role = session()->get('role');
        $id_bag = $this->request->getPost('id_bag');

        if ($role === 'rs') {
            session()->setFlashdata('error', 'Akses tidak diizinkan.');
            return redirect()->to('/distribusi');
        }

        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser(session()->get('id_user'));
            $stok = $this->stokModel->find($id_bag);
            if (!$ownProdusen || !$stok || $stok['id_produsen'] != $ownProdusen['id_produsen']) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/distribusi');
            }
        }

        $data = [
            'id_bag' => $id_bag,
            'id_rs' => $this->request->getPost('id_rs'),
            'tanggal_distribusi' => $this->request->getPost('tanggal_distribusi'),
            'penerima' => $this->request->getPost('penerima'),
            'keperluan' => $this->request->getPost('keperluan'),
            'no_permintaan' => $this->request->getPost('no_permintaan')
        ];

        $stokData = ['status' => 'terdistribusi'];

        if ($this->distribusiModel->save($data) && $this->stokModel->update($this->request->getPost('id_bag'), $stokData)) {
            // After marking stok as distributed, check low stock thresholds and notify admin if needed
            try {
                $bag = $this->stokModel->find($id_bag);
                if ($bag) {
                    $thresholds = [ 'A' => 10, 'B' => 10, 'O' => 15, 'AB' => 5 ];
                    $gol = $bag['gol_dar'] ?? null;
                    $jenis = $bag['jenis_darah'] ?? null;
                    $prodId = $bag['id_produsen'] ?? null;
                    if ($gol && isset($thresholds[$gol]) && $prodId) {
                        $remaining = $this->stokModel->countAvailableByProdusenGolonganJenis($prodId, $gol, $jenis);
                        if ($remaining < $thresholds[$gol]) {
                            $produsen = $this->produsenModel->find($prodId);
                            $produsenName = $produsen['nama'] ?? ('BDRS #' . $prodId);
                            $notificationModel = new \App\Models\NotificationModel();
                            $adminUsers = (new \App\Models\UserModel())->where('role', 'admin')->findAll();
                            foreach ($adminUsers as $admin) {
                                $notificationModel->insert([
                                    'user_id' => $admin['id_user'],
                                    'title' => 'Stok Rendah: ' . $gol . ' ' . $jenis,
                                    'message' => 'Stok kantong darah ' . $gol . ' ' . $jenis . ' untuk BDRS ' . $produsenName . ' tersisa ' . $remaining . ' dan di bawah batas minimal.',
                                    'is_read' => 0,
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // ignore notification errors
            }

            session()->setFlashdata('success', 'Distribusi darah berhasil dicatat');
        } else {
            session()->setFlashdata('error', 'Gagal mencatat distribusi darah');
        }

        return redirect()->to('/distribusi');
    }

    public function delete($id)
    {
        $distribusi = $this->distribusiModel->find($id);
        if (!$distribusi) {
            session()->setFlashdata('error', 'Data distribusi tidak ditemukan');
            return redirect()->to('/distribusi');
        }

        $role = session()->get('role');
        $userId = session()->get('id_user');
        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser($userId);
            if (!$ownProdusen || $distribusi['id_produsen'] != $ownProdusen['id_produsen']) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/distribusi');
            }
        }
        if ($role === 'rs') {
            $ownRs = $this->rumahSakitModel->getRumahSakitByUser($userId);
            if (!$ownRs && session()->has('id_rs')) {
                $ownRs = $this->rumahSakitModel->find(session()->get('id_rs'));
            }
            if (!$ownRs && session()->has('nama')) {
                $ownRs = $this->rumahSakitModel->where('nama_rs', session()->get('nama'))->first();
            }
            if (!$ownRs || $distribusi['id_rs'] != $ownRs['id_rs']) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/distribusi');
            }
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            $stokUpdated = false;
            if (!empty($distribusi['id_bag'])) {
                $stokUpdated = $this->stokModel->update($distribusi['id_bag'], ['status' => 'tersedia']);
            }

            $deleted = $this->distribusiModel->delete($id);
            $db->transComplete();

            if ($deleted) {
                session()->setFlashdata('success', 'Data distribusi berhasil dihapus');
            } else {
                session()->setFlashdata('error', 'Gagal menghapus data distribusi');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to('/distribusi');
    }

    public function edit($id)
    {
        $distribusi = $this->distribusiModel->select('distribusi.*, stok.no_kantong, stok.gol_dar, stok.jenis_darah, stok.rhesus, rumah_sakit.nama_rs, produsen.nama as nama_produsen')
                                           ->join('stok', 'stok.id_bag = distribusi.id_bag')
                                           ->join('rumah_sakit', 'rumah_sakit.id_rs = distribusi.id_rs', 'left')
                                           ->join('produsen', 'produsen.id_produsen = stok.id_produsen', 'left')
                                           ->where('distribusi.id_distribusi', $id)
                                           ->first();

        if (!$distribusi) {
            session()->setFlashdata('error', 'Data distribusi tidak ditemukan');
            return redirect()->to('/distribusi');
        }

        $role = session()->get('role');
        $userId = session()->get('id_user');
        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser($userId);
            if (!$ownProdusen || $distribusi['id_produsen'] != $ownProdusen['id_produsen']) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/distribusi');
            }
        }
        if ($role === 'rs') {
            $ownRs = $this->rumahSakitModel->getRumahSakitByUser($userId);
            if (!$ownRs && session()->has('id_rs')) {
                $ownRs = $this->rumahSakitModel->find(session()->get('id_rs'));
            }
            if (!$ownRs && session()->has('nama')) {
                $ownRs = $this->rumahSakitModel->where('nama_rs', session()->get('nama'))->first();
            }
            if (!$ownRs || $distribusi['id_rs'] != $ownRs['id_rs']) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/distribusi');
            }
        }

        $rumahSakit = $this->rumahSakitModel->findAll();

        return view('distribusi/edit', [
            'title' => 'Edit Distribusi',
            'page_title' => 'Edit Data Distribusi',
            'distribusi' => $distribusi,
            'rumah_sakit' => $rumahSakit,
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id)
    {
        $distribusi = $this->distribusiModel->find($id);
        if (!$distribusi) {
            session()->setFlashdata('error', 'Data distribusi tidak ditemukan');
            return redirect()->to('/distribusi');
        }

        $role = session()->get('role');
        $userId = session()->get('id_user');
        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser($userId);
            if (!$ownProdusen || $distribusi['id_produsen'] != $ownProdusen['id_produsen']) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/distribusi');
            }
        }
        if ($role === 'rs') {
            $ownRs = $this->rumahSakitModel->getRumahSakitByUser($userId);
            if (!$ownRs && session()->has('id_rs')) {
                $ownRs = $this->rumahSakitModel->find(session()->get('id_rs'));
            }
            if (!$ownRs && session()->has('nama')) {
                $ownRs = $this->rumahSakitModel->where('nama_rs', session()->get('nama'))->first();
            }
            if (!$ownRs || $distribusi['id_rs'] != $ownRs['id_rs']) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/distribusi');
            }
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_rs' => 'required',
            'penerima' => 'required',
            'tanggal_distribusi' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'id_rs' => $this->request->getPost('id_rs'),
            'tanggal_distribusi' => $this->request->getPost('tanggal_distribusi'),
            'penerima' => $this->request->getPost('penerima'),
            'keperluan' => $this->request->getPost('keperluan'),
            'no_permintaan' => $this->request->getPost('no_permintaan')
        ];

        if ($this->distribusiModel->update($id, $data)) {
            session()->setFlashdata('success', 'Data distribusi berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data distribusi');
        }

        return redirect()->to('/distribusi');
    }
}
