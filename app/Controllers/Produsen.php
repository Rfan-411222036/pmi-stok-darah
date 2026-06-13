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
        $role = session()->get('role');
        $userId = session()->get('id_user');

        if ($role === 'admin') {
            $result = $this->produsenModel->getProdusen($search, $perPage);
        } elseif ($role === 'bdrs') {
            $result = $this->produsenModel->getProdusen($search, $perPage, $userId);
        } else {
            $result = [
                'produsen' => [],
                'pager' => $this->produsenModel->pager
            ];
        }

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
        $role = session()->get('role');

        if ($role === 'rs') {
            session()->setFlashdata('error', 'Akses tidak diizinkan.');
            return redirect()->to('/produsen');
        }

        $data = [
            'title' => 'Tambah BDRS',
            'page_title' => 'Tambah Data BDRS',
            'validation' => \Config\Services::validation()
        ];
        return view('produsen/create', $data);
    }

    public function store()
    {
        $role = session()->get('role');

        if ($role === 'rs') {
            session()->setFlashdata('error', 'Akses tidak diizinkan.');
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
            'nama' => $this->request->getPost('nama'),
            'jenis' => $this->request->getPost('jenis'),
            'no_kantong' => $this->request->getPost('no_kantong'),
            'status' => $this->request->getPost('status'),
            'alamat' => $this->request->getPost('alamat'),
            'telepon' => $this->request->getPost('telepon')
        ];

        if ($role === 'bdrs') {
            $data['id_user'] = session()->get('id_user');
        }

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
        $role = session()->get('role');

        if (!$produsen) {
            session()->setFlashdata('error', 'Data produsen tidak ditemukan');
            return redirect()->to('/produsen');
        }

        if ($role !== 'admin') {
            if ($role === 'bdrs') {
                if (isset($produsen['id_user']) && $produsen['id_user'] != session()->get('id_user')) {
                    session()->setFlashdata('error', 'Akses tidak diizinkan.');
                    return redirect()->to('/produsen');
                }
            } else {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/produsen');
            }
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
        $role = session()->get('role');

        if (!$produsen) {
            session()->setFlashdata('error', 'Data produsen tidak ditemukan');
            return redirect()->to('/produsen');
        }

        if ($role !== 'admin') {
            if ($role === 'bdrs') {
                if (isset($produsen['id_user']) && $produsen['id_user'] != session()->get('id_user')) {
                    session()->setFlashdata('error', 'Akses tidak diizinkan.');
                    return redirect()->to('/produsen');
                }
            } else {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/produsen');
            }
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
            'nama' => $this->request->getPost('nama'),
            'jenis' => $this->request->getPost('jenis'),
            'no_kantong' => $this->request->getPost('no_kantong'),
            'status' => $this->request->getPost('status'),
            'alamat' => $this->request->getPost('alamat'),
            'telepon' => $this->request->getPost('telepon')
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
        $role = session()->get('role');

        if (!$produsen) {
            session()->setFlashdata('error', 'Data produsen tidak ditemukan');
            return redirect()->to('/produsen');
        }

        if ($role !== 'admin') {
            if ($role === 'bdrs') {
                if (isset($produsen['id_user']) && $produsen['id_user'] != session()->get('id_user')) {
                    session()->setFlashdata('error', 'Akses tidak diizinkan.');
                    return redirect()->to('/produsen');
                }
            } else {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/produsen');
            }
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
