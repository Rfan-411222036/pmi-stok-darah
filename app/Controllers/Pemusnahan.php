<?php

namespace App\Controllers;

use App\Models\PemusnahanModel;
use App\Models\StokModel;

class Pemusnahan extends BaseController
{
    protected $pemusnahanModel;
    protected $stokModel;

    public function __construct()
    {
        $this->pemusnahanModel = new PemusnahanModel();
        $this->stokModel = new StokModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $perPage = 10;

        $result = $this->pemusnahanModel->getPemusnahanWithDetails($search, $perPage);

        $data = [
            'title' => 'Management Pemusnahan',
            'page_title' => 'Data Pemusnahan Darah',
            'pemusnahan' => $result['pemusnahan'] ?? [],
            'pager' => $result['pager'] ?? $this->pemusnahanModel->pager,
            'search' => $search
        ];

        return view('pemusnahan/index', $data);
    }

    public function create()
    {
        $stokExpired = $this->stokModel->where('status', 'tersedia')
                                      ->where('tanggal_expired <', date('Y-m-d'))
                                      ->findAll();

        $stokRusak = $this->stokModel->where('status', 'tersedia')
                                    ->findAll();

        $data = [
            'title' => 'Tambah Pemusnahan',
            'page_title' => 'Tambah Data Pemusnahan',
            'stok_expired' => $stokExpired,
            'stok_rusak' => $stokRusak,
            'validation' => \Config\Services::validation()
        ];
        return view('pemusnahan/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_bag' => 'required',
            'tanggal_pemusnahan' => 'required',
            'alasan' => 'required',
            'petugas' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'id_bag' => $this->request->getPost('id_bag'),
            'tanggal_pemusnahan' => $this->request->getPost('tanggal_pemusnahan'),
            'alasan' => $this->request->getPost('alasan'),
            'keterangan' => $this->request->getPost('keterangan'),
            'petugas' => $this->request->getPost('petugas')
        ];

        $stokData = ['status' => 'musnah'];

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            $pemusnahanSaved = $this->pemusnahanModel->save($data);
            $stokUpdated = $this->stokModel->update($this->request->getPost('id_bag'), $stokData);

            $db->transComplete();

            if ($pemusnahanSaved && $stokUpdated) {
                // After pemusnahan, check low-stock thresholds and notify admin if needed
                try {
                    $bag = $this->stokModel->find($this->request->getPost('id_bag'));
                    if ($bag) {
                        $thresholds = [ 'A' => 10, 'B' => 10, 'O' => 15, 'AB' => 5 ];
                        $gol = $bag['gol_dar'] ?? null;
                        $jenis = $bag['jenis_darah'] ?? null;
                        $prodId = $bag['id_produsen'] ?? null;
                        if ($gol && isset($thresholds[$gol]) && $prodId) {
                            $produsenModel = new \App\Models\ProdusenModel();
                            $produsen = $produsenModel->find($prodId);
                            $produsenName = $produsen['nama'] ?? ('BDRS #' . $prodId);
                            $remaining = $this->stokModel->countAvailableByProdusenGolonganJenis($prodId, $gol, $jenis);
                            if ($remaining < $thresholds[$gol]) {
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

                session()->setFlashdata('success', 'Data pemusnahan berhasil dicatat');
            } else {
                session()->setFlashdata('error', 'Gagal mencatat data pemusnahan');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to('/pemusnahan');
    }

    public function delete($id)
    {
        $pemusnahan = $this->pemusnahanModel->find($id);
        if (!$pemusnahan) {
            session()->setFlashdata('error', 'Data pemusnahan tidak ditemukan');
            return redirect()->to('/pemusnahan');
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            $stokRestored = false;
            if (!empty($pemusnahan['id_bag'])) {
                $stokRestored = $this->stokModel->update($pemusnahan['id_bag'], ['status' => 'tersedia']);
            }

            $deleted = $this->pemusnahanModel->delete($id);
            $db->transComplete();

            if ($deleted) {
                session()->setFlashdata('success', 'Data pemusnahan berhasil dihapus');
            } else {
                session()->setFlashdata('error', 'Gagal menghapus data pemusnahan');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to('/pemusnahan');
    }

    public function edit($id)
    {
        $pemusnahan = $this->pemusnahanModel->select('pemusnahan.*, stok.no_kantong, stok.gol_dar, stok.jenis_darah, stok.tanggal_expired')
                                           ->join('stok', 'stok.id_bag = pemusnahan.id_bag')
                                           ->where('pemusnahan.id_pemusnahan', $id)
                                           ->first();

        if (!$pemusnahan) {
            session()->setFlashdata('error', 'Data pemusnahan tidak ditemukan');
            return redirect()->to('/pemusnahan');
        }

        return view('pemusnahan/edit', [
            'title' => 'Edit Pemusnahan',
            'page_title' => 'Edit Data Pemusnahan',
            'pemusnahan' => $pemusnahan,
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id)
    {
        $pemusnahan = $this->pemusnahanModel->find($id);
        if (!$pemusnahan) {
            session()->setFlashdata('error', 'Data pemusnahan tidak ditemukan');
            return redirect()->to('/pemusnahan');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'tanggal_pemusnahan' => 'required',
            'alasan' => 'required',
            'petugas' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'tanggal_pemusnahan' => $this->request->getPost('tanggal_pemusnahan'),
            'alasan' => $this->request->getPost('alasan'),
            'keterangan' => $this->request->getPost('keterangan'),
            'petugas' => $this->request->getPost('petugas')
        ];

        if ($this->pemusnahanModel->update($id, $data)) {
            session()->setFlashdata('success', 'Data pemusnahan berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data pemusnahan');
        }

        return redirect()->to('/pemusnahan');
    }
}
