<?php

namespace App\Controllers;

use App\Models\ReturnModel;
use App\Models\DistribusiModel;
use App\Models\StokModel;
use App\Models\RumahSakitModel;

class Retur extends BaseController
{
    protected $returnModel;
    protected $distribusiModel;
    protected $stokModel;
    protected $rumahSakitModel;

    public function __construct()
    {
        $this->returnModel = new ReturnModel();
        $this->distribusiModel = new DistribusiModel();
        $this->stokModel = new StokModel();
        $this->rumahSakitModel = new RumahSakitModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $perPage = 10;

        $result = $this->returnModel->getReturnWithDetails($search, $perPage);

        $data = [
            'title' => 'Management Return Darah',
            'page_title' => 'Data Return Darah',
            'return' => $result['return'] ?? [],
            'pager' => $result['pager'] ?? $this->returnModel->pager,
            'search' => $search
        ];

        return view('return/index', $data);
    }

    public function create()
    {
        $distribusi = $this->returnModel->getDistribusiForReturn();
        $role = session()->get('role');
        $userId = session()->get('id_user');

        if ($role === 'rs') {
            $rs = $this->rumahSakitModel->getRumahSakitByUser($userId);
            $rumahSakit = $rs ? [$rs] : [];
        } else {
            $rumahSakit = $this->rumahSakitModel->findAll();
        }

        $data = [
            'title' => 'Tambah Return Darah',
            'page_title' => 'Tambah Data Return Darah',
            'distribusi' => $distribusi,
            'rumah_sakit' => $rumahSakit,
            'validation' => \Config\Services::validation()
        ];
        return view('return/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_distribusi' => 'required',
            'id_bag' => 'required',
            'id_rs' => 'required',
            'tanggal_retur' => 'required',
            'alasan_return' => 'required',
            'kondisi_darah' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $id_distribusi = $this->request->getPost('id_distribusi');
        $distribusiRecord = $this->distribusiModel->find($id_distribusi);

        $data = [
            'id_distribusi' => $id_distribusi,
            'id_bag' => $this->request->getPost('id_bag'),
            'id_rs' => $distribusiRecord['id_rs'] ?? null,
            'tanggal_retur' => $this->request->getPost('tanggal_retur'),
            'alasan_return' => $this->request->getPost('alasan_return'),
            'kondisi_darah' => $this->request->getPost('kondisi_darah'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        $statusStok = $this->request->getPost('kondisi_darah') == 'baik' ? 'tersedia' : 'musnah';
        $stokData = ['status' => $statusStok];

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            $returnSaved = $this->returnModel->save($data);
            $stokUpdated = $this->stokModel->update($this->request->getPost('id_bag'), $stokData);

            $db->transComplete();

            if ($returnSaved && $stokUpdated) {
                session()->setFlashdata('success', 'Data return berhasil dicatat. Status stok diperbarui.');
            } else {
                session()->setFlashdata('error', 'Gagal mencatat data return');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to('/return');
    }

    public function delete($id)
    {
        $retur = $this->returnModel->find($id);
        if (!$retur) {
            session()->setFlashdata('error', 'Data return tidak ditemukan');
            return redirect()->to('/return');
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            $status = 'terdistribusi';
            if (!empty($retur['id_bag'])) {
                $this->stokModel->update($retur['id_bag'], ['status' => $status]);
            }

            $deleted = $this->returnModel->delete($id);
            $db->transComplete();

            if ($deleted) {
                session()->setFlashdata('success', 'Data return berhasil dihapus');
            } else {
                session()->setFlashdata('error', 'Gagal menghapus data return');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to('/return');
    }

    public function edit($id)
    {
        $retur = $this->returnModel->select('return_darah.*, stok.no_kantong, stok.gol_dar, stok.jenis_darah, stok.tanggal_expired, rumah_sakit.nama_rs, distribusi.penerima')
                                   ->join('stok', 'stok.id_bag = return_darah.id_bag')
                                   ->join('rumah_sakit', 'rumah_sakit.id_rs = return_darah.id_rs', 'left')
                                   ->join('distribusi', 'distribusi.id_distribusi = return_darah.id_distribusi', 'left')
                                   ->where('return_darah.id_return', $id)
                                   ->first();

        if (!$retur) {
            session()->setFlashdata('error', 'Data return tidak ditemukan');
            return redirect()->to('/return');
        }

        return view('return/edit', [
            'title' => 'Edit Return',
            'page_title' => 'Edit Data Return',
            'return' => $retur,
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id)
    {
        $retur = $this->returnModel->find($id);
        if (!$retur) {
            session()->setFlashdata('error', 'Data return tidak ditemukan');
            return redirect()->to('/return');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'tanggal_retur' => 'required',
            'alasan_return' => 'required',
            'kondisi_darah' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $oldReturn = $this->returnModel->find($id);
        $newCondition = $this->request->getPost('kondisi_darah');
        $stokStatus = $newCondition === 'baik' ? 'tersedia' : 'musnah';

        $data = [
            'tanggal_retur' => $this->request->getPost('tanggal_retur'),
            'alasan_return' => $this->request->getPost('alasan_return'),
            'kondisi_darah' => $newCondition,
            'keterangan' => $this->request->getPost('keterangan')
        ];

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            $returnUpdated = $this->returnModel->update($id, $data);
            $stokUpdated = $this->stokModel->update($retur['id_bag'], ['status' => $stokStatus]);

            $db->transComplete();

            if ($returnUpdated && $stokUpdated) {
                session()->setFlashdata('success', 'Data return berhasil diupdate');
            } else {
                session()->setFlashdata('error', 'Gagal mengupdate data return');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to('/return');
    }

    public function getDistribusiInfo($id_distribusi)
    {
        $distribusi = $this->distribusiModel->select('distribusi.*, stok.no_kantong, stok.gol_dar, stok.jenis_darah, stok.tanggal_expired, rumah_sakit.nama_rs')
                                           ->join('stok', 'stok.id_bag = distribusi.id_bag')
                                           ->join('rumah_sakit', 'rumah_sakit.id_rs = distribusi.id_rs')
                                           ->where('distribusi.id_distribusi', $id_distribusi)
                                           ->first();

        if ($distribusi) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $distribusi
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data distribusi tidak ditemukan'
            ]);
        }
    }
}
