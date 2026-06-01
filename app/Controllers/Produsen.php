<?php

namespace App\Controllers;

use App\Models\ProdusenModel;

class Produsen extends BaseController
{
    protected $produsenModel;

    public function __construct()
    {
        $this->produsenModel = new ProdusenModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $perPage = 10;

        $result = $this->produsenModel->getProdusen($search, $perPage);

        $data = [
            'title' => 'Management BDRS',
            'page_title' => 'Data BDRS Darah',
            'produsen' => $result['produsen'],
            'pager' => $result['pager'],
            'search' => $search
        ];

        return view('produsen/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah BDRS',
            'page_title' => 'Tambah Data BDRS',
            'validation' => \Config\Services::validation()
        ];
        return view('produsen/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama' => 'required',
            'jenis' => 'required',
            'no_kantong' => 'required',
            'status' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nama'           => $this->request->getPost('nama'),
            'jenis'          => $this->request->getPost('jenis'),
            'jenis_darah'    => $this->request->getPost('jenis_darah'),
            'no_kantong'     => $this->request->getPost('no_kantong'),
            'status'         => $this->request->getPost('status'),
            'alamat'         => $this->request->getPost('alamat'),
            'is_central_hub' => $this->request->getPost('is_central_hub') ? 1 : 0,
            'min_threshold'  => (int) ($this->request->getPost('min_threshold') ?? 30),
            'priority_order' => (int) ($this->request->getPost('priority_order') ?? 0),
        ];

        if ($this->produsenModel->save($data)) {
            session()->setFlashdata('success', 'Data produsen berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data produsen');
        }

        return redirect()->to('/produsen');
    }

    public function edit($id)
    {
        $produsen = $this->produsenModel->find($id);

        if (!$produsen) {
            session()->setFlashdata('error', 'Data produsen tidak ditemukan');
            return redirect()->to('/produsen');
        }

        $data = [
            'title' => 'Edit Produsen',
            'page_title' => 'Edit Data Produsen',
            'produsen' => $produsen,
            'validation' => \Config\Services::validation()
        ];
        return view('produsen/edit', $data);
    }

    public function update($id)
    {
        $produsen = $this->produsenModel->find($id);

        if (!$produsen) {
            session()->setFlashdata('error', 'Data produsen tidak ditemukan');
            return redirect()->to('/produsen');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama' => 'required',
            'jenis' => 'required',
            'no_kantong' => 'required',
            'status' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nama'           => $this->request->getPost('nama'),
            'jenis'          => $this->request->getPost('jenis'),
            'jenis_darah'    => $this->request->getPost('jenis_darah'),
            'no_kantong'     => $this->request->getPost('no_kantong'),
            'status'         => $this->request->getPost('status'),
            'alamat'         => $this->request->getPost('alamat'),
            'is_central_hub' => $this->request->getPost('is_central_hub') ? 1 : 0,
            'min_threshold'  => (int) ($this->request->getPost('min_threshold') ?? 30),
            'priority_order' => (int) ($this->request->getPost('priority_order') ?? 0),
        ];

        if ($this->produsenModel->update($id, $data)) {
            session()->setFlashdata('success', 'Data produsen berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data produsen');
        }

        return redirect()->to('/produsen');
    }

    public function delete($id)
    {
        $produsen = $this->produsenModel->find($id);

        if (!$produsen) {
            session()->setFlashdata('error', 'Data produsen tidak ditemukan');
            return redirect()->to('/produsen');
        }

        $stokModel = new \App\Models\StokModel();
        $stokCount = $stokModel->where('id_produsen', $id)->where('status', 'tersedia')->countAllResults();

        if ($stokCount > 0) {
            session()->setFlashdata('error', 'Tidak dapat menghapus produsen karena masih memiliki data stok darah aktif. Hapus atau distribusikan terlebih dahulu data stok yang terkait.');
            return redirect()->to('/produsen');
        }

        try {
            if ($this->produsenModel->softDelete($id)) {
                session()->setFlashdata('success', 'Data produsen berhasil dihapus');
            } else {
                session()->setFlashdata('error', 'Gagal menghapus data produsen');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to('/produsen');
    }
}
