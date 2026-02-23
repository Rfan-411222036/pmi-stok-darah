<?php

namespace App\Controllers;

use App\Models\RumahSakitModel;

class RumahSakit extends BaseController
{
    protected $rumahSakitModel;

    public function __construct()
    {
        $this->rumahSakitModel = new RumahSakitModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $perPage = 10;

        $result = $this->rumahSakitModel->getRumahSakit($search, $perPage);

        $data = [
            'title' => 'Management Rumah Sakit',
            'page_title' => 'Data Rumah Sakit',
            'rumah_sakit' => $result['rumah_sakit'],
            'pager' => $result['pager'],
            'search' => $search
        ];

        return view('rumahsakit/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Rumah Sakit',
            'page_title' => 'Tambah Data Rumah Sakit',
            'validation' => \Config\Services::validation()
        ];
        return view('rumahsakit/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama_rs' => 'required',
            'jenis_rs' => 'required',
            'telepon' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nama_rs' => $this->request->getPost('nama_rs'),
            'alamat' => $this->request->getPost('alamat'),
            'telepon' => $this->request->getPost('telepon'),
            'email' => $this->request->getPost('email'),
            'jenis_rs' => $this->request->getPost('jenis_rs')
        ];

        if ($this->rumahSakitModel->save($data)) {
            session()->setFlashdata('success', 'Data rumah sakit berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data rumah sakit');
        }

        return redirect()->to('/rumahsakit');
    }

    public function edit($id)
    {
        $rumahSakit = $this->rumahSakitModel->find($id);

        if (!$rumahSakit) {
            session()->setFlashdata('error', 'Data rumah sakit tidak ditemukan');
            return redirect()->to('/rumahsakit');
        }

        $data = [
            'title' => 'Edit Rumah Sakit',
            'page_title' => 'Edit Data Rumah Sakit',
            'rumah_sakit' => $rumahSakit,
            'validation' => \Config\Services::validation()
        ];
        return view('rumahsakit/edit', $data);
    }

    public function update($id)
    {
        $rumahSakit = $this->rumahSakitModel->find($id);

        if (!$rumahSakit) {
            session()->setFlashdata('error', 'Data rumah sakit tidak ditemukan');
            return redirect()->to('/rumahsakit');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama_rs' => 'required',
            'jenis_rs' => 'required',
            'telepon' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nama_rs' => $this->request->getPost('nama_rs'),
            'alamat' => $this->request->getPost('alamat'),
            'telepon' => $this->request->getPost('telepon'),
            'email' => $this->request->getPost('email'),
            'jenis_rs' => $this->request->getPost('jenis_rs')
        ];

        if ($this->rumahSakitModel->update($id, $data)) {
            session()->setFlashdata('success', 'Data rumah sakit berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data rumah sakit');
        }

        return redirect()->to('/rumahsakit');
    }

    public function delete($id)
    {
        $rumahSakit = $this->rumahSakitModel->find($id);

        if (!$rumahSakit) {
            session()->setFlashdata('error', 'Data rumah sakit tidak ditemukan');
            return redirect()->to('/rumahsakit');
        }

        // Cek apakah rumah sakit memiliki data distribusi aktif
        $distribusiModel = new \App\Models\DistribusiModel();
        $distribusiCount = $distribusiModel->where('idrs', $id)->countAllResults();

        if ($distribusiCount > 0) {
            session()->setFlashdata('error', 'Tidak dapat menghapus rumah sakit karena masih memiliki riwayat distribusi.');
            return redirect()->to('/rumahsakit');
        }

        try {
            // Gunakan soft delete
            if ($this->rumahSakitModel->softDelete($id)) {
                session()->setFlashdata('success', 'Data rumah sakit berhasil dihapus');
            } else {
                session()->setFlashdata('error', 'Gagal menghapus data rumah sakit');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to('/rumahsakit');
    }
}