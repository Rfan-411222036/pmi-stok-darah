<?php

namespace App\Controllers;

use App\Models\DistribusiModel;
use App\Models\StokModel;
use App\Models\RumahSakitModel;

class Distribusi extends BaseController
{
    protected $distribusiModel;
    protected $stokModel;
    protected $rumahSakitModel;

    public function __construct()
    {
        $this->distribusiModel = new DistribusiModel();
        $this->stokModel = new StokModel();
        $this->rumahSakitModel = new RumahSakitModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $perPage = 10;

        $result = $this->distribusiModel->getDistribusiWithDetails($search, $perPage);

        $data = [
            'title' => 'Management Distribusi',
            'page_title' => 'Data Distribusi Darah',
            'distribusi' => $result['distribusi'],
            'pager' => $result['pager'],
            'search' => $search
        ];

        return view('distribusi/index', $data);
    }

    public function create()
    {
        $stok = $this->stokModel->getStokForDistribusi();
        $rumahSakit = $this->rumahSakitModel->findAll();

        $data = [
            'title' => 'Tambah Distribusi',
            'page_title' => 'Tambah Distribusi Darah',
            'stok' => $stok,
            'rumah_sakit' => $rumahSakit,
            'validation' => \Config\Services::validation()
        ];
        return view('distribusi/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'idbag' => 'required',
            'idrs' => 'required',
            'penerima' => 'required',
            'tanggal_distribusi' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'idbag' => $this->request->getPost('idbag'),
            'idrs' => $this->request->getPost('idrs'),
            'tanggal_distribusi' => $this->request->getPost('tanggal_distribusi'),
            'penerima' => $this->request->getPost('penerima'),
            'keperluan' => $this->request->getPost('keperluan'),
            'no_permintaan' => $this->request->getPost('no_permintaan')
        ];

        // Update status stok menjadi terdistribusi
        $stokData = [
            'status' => 'terdistribusi'
        ];

        if ($this->distribusiModel->save($data) && $this->stokModel->update($this->request->getPost('idbag'), $stokData)) {
            session()->setFlashdata('success', 'Distribusi darah berhasil dicatat');
        } else {
            session()->setFlashdata('error', 'Gagal mencatat distribusi darah');
        }

        return redirect()->to('/distribusi');
    }
}