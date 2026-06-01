<?php

namespace App\Controllers;

use App\Models\StokModel;
use App\Models\ProdusenModel;

class Stok extends BaseController
{
    protected $stokModel;
    protected $produsenModel;

    public function __construct()
    {
        $this->stokModel = new StokModel();
        $this->produsenModel = new ProdusenModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $perPage = 10;

        $result = $this->stokModel->getStokWithDetails($search, $perPage);
        $golonganRhesus = $this->stokModel->getStokByGolonganRhesus();

        $bloodGroups = ['A', 'B', 'AB', 'O'];
        $rhesusCounts = [
            '+' => array_fill_keys($bloodGroups, 0),
            '-' => array_fill_keys($bloodGroups, 0),
        ];

        foreach ($golonganRhesus as $item) {
            $group = $item['goldar'];
            $rhesus = $item['rhesus'] ?? '+';

            if (isset($rhesusCounts[$rhesus][$group])) {
                $rhesusCounts[$rhesus][$group] = (int) $item['total'];
            }
        }

        // Debug: Cek struktur data yang dikembalikan
        // echo "<pre>"; print_r($result); echo "</pre>"; die();

        $data = [
            'title' => 'Management Stok Darah',
            'page_title' => 'Data Stok Darah',
            'stok' => $result['stok'] ?? [], // Gunakan null coalescing operator
            'pager' => $result['pager'] ?? $this->stokModel->pager,
            'search' => $search,
            'chartLabels' => $bloodGroups,
            'chartDataPlus' => array_values($rhesusCounts['+']),
            'chartDataMinus' => array_values($rhesusCounts['-']),
            'stockAvailable' => $this->stokModel->getStokTersedia(),
            'stockNearExpire' => $this->stokModel->getStokMendekatiExpired(),
            'stockExpired' => $this->stokModel->getStokExpired(),
        ];

        return view('stok/index', $data);
    }

    public function create()
    {
        $produsen = $this->produsenModel->findAll();

        $data = [
            'title' => 'Tambah Stok Darah',
            'page_title' => 'Tambah Stok Darah',
            'produsen' => $produsen,
            'validation' => \Config\Services::validation()
        ];
        return view('stok/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'no_kantong' => 'required|is_unique[stok.no_kantong]',
            'idprodusen' => 'required',
            'jenisdarah' => 'required',
            'goldar' => 'required',
            'volume' => 'required|numeric',
            'tanggal_produksi' => 'required',
            'tanggal_expired' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'no_kantong' => $this->request->getPost('no_kantong'),
            'idprodusen' => $this->request->getPost('idprodusen'),
            'jenisdarah' => $this->request->getPost('jenisdarah'),
            'goldar' => $this->request->getPost('goldar'),
            'rhesus' => $this->request->getPost('rhesus'),
            'volume' => $this->request->getPost('volume'),
            'tanggal_produksi' => $this->request->getPost('tanggal_produksi'),
            'tanggal_expired' => $this->request->getPost('tanggal_expired'),
            'status' => 'tersedia',
            'keterangan' => $this->request->getPost('keterangan')
        ];

        if ($this->stokModel->save($data)) {
            session()->setFlashdata('success', 'Stok darah berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan stok darah');
        }

        return redirect()->to('/stok');
    }

    public function edit($id)
    {
        $stok = $this->stokModel->find($id);
        $produsen = $this->produsenModel->findAll();

        if (!$stok) {
            session()->setFlashdata('error', 'Data stok tidak ditemukan');
            return redirect()->to('/stok');
        }

        $data = [
            'title' => 'Edit Stok Darah',
            'page_title' => 'Edit Stok Darah',
            'stok' => $stok,
            'produsen' => $produsen,
            'validation' => \Config\Services::validation()
        ];
        return view('stok/edit', $data);
    }

    public function update($id)
    {
        $stok = $this->stokModel->find($id);

        if (!$stok) {
            session()->setFlashdata('error', 'Data stok tidak ditemukan');
            return redirect()->to('/stok');
        }

        $noKantongRules = $stok['no_kantong'] === $this->request->getPost('no_kantong') ?
            'required' : 'required|is_unique[stok.no_kantong]';

        $validation = \Config\Services::validation();
        $validation->setRules([
            'no_kantong' => $noKantongRules,
            'idprodusen' => 'required',
            'jenisdarah' => 'required',
            'goldar' => 'required',
            'volume' => 'required|numeric',
            'tanggal_produksi' => 'required',
            'tanggal_expired' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'no_kantong' => $this->request->getPost('no_kantong'),
            'idprodusen' => $this->request->getPost('idprodusen'),
            'jenisdarah' => $this->request->getPost('jenisdarah'),
            'goldar' => $this->request->getPost('goldar'),
            'rhesus' => $this->request->getPost('rhesus'),
            'volume' => $this->request->getPost('volume'),
            'tanggal_produksi' => $this->request->getPost('tanggal_produksi'),
            'tanggal_expired' => $this->request->getPost('tanggal_expired'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        if ($this->stokModel->update($id, $data)) {
            session()->setFlashdata('success', 'Stok darah berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate stok darah');
        }

        return redirect()->to('/stok');
    }

    public function delete($id)
    {
        $stok = $this->stokModel->find($id);

        if (!$stok) {
            session()->setFlashdata('error', 'Data stok tidak ditemukan');
            return redirect()->to('/stok');
        }

        // Cek apakah stok sudah didistribusikan
        if ($stok['status'] === 'terdistribusi') {
            session()->setFlashdata('error', 'Tidak dapat menghapus stok yang sudah didistribusikan');
            return redirect()->to('/stok');
        }

        // Cek apakah stok sudah dimusnahkan
        if ($stok['status'] === 'musnah') {
            session()->setFlashdata('error', 'Tidak dapat menghapus stok yang sudah dimusnahkan');
            return redirect()->to('/stok');
        }

        // Cek apakah stok memiliki data distribusi
        $distribusiModel = new \App\Models\DistribusiModel();
        $distribusiCount = $distribusiModel->where('idbag', $id)->countAllResults();

        if ($distribusiCount > 0) {
            session()->setFlashdata('error', 'Tidak dapat menghapus stok karena sudah didistribusikan');
            return redirect()->to('/stok');
        }

        // Cek apakah stok memiliki data pemusnahan
        $pemusnahanModel = new \App\Models\PemusnahanModel();
        $pemusnahanCount = $pemusnahanModel->where('idbag', $id)->countAllResults();

        if ($pemusnahanCount > 0) {
            session()->setFlashdata('error', 'Tidak dapat menghapus stok karena sudah dimusnahkan');
            return redirect()->to('/stok');
        }

        try {
            if ($this->stokModel->delete($id)) {
                session()->setFlashdata('success', 'Stok darah berhasil dihapus');
            } else {
                session()->setFlashdata('error', 'Gagal menghapus stok darah');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to('/stok');
    }
}
