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

        $result = $this->distribusiModel->getDistribusiWithDetails($search, $perPage);

        // provide list of produsen for request form
        $produsenList = $this->produsenModel->orderBy('nama')->findAll();

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
            'rumah_sakit' => $rumahSakitList
            ,'golongan_list' => $golonganList
            ,'jenis_list' => $jenisList
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
            'id_bag' => 'required',
            'id_rs' => 'required',
            'penerima' => 'required',
            'tanggal_distribusi' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $id_bag = $this->request->getPost('id_bag');
        $id_produsen = null;
        if ($id_bag) {
            $stok = $this->stokModel->find($id_bag);
            if ($stok && isset($stok['id_produsen'])) {
                $id_produsen = $stok['id_produsen'];
            }
        }

        $data = [
            'id_bag' => $id_bag,
            'id_rs' => $this->request->getPost('id_rs'),
            'id_produsen' => $id_produsen,
            'tanggal_distribusi' => $this->request->getPost('tanggal_distribusi'),
            'penerima' => $this->request->getPost('penerima'),
            'keperluan' => $this->request->getPost('keperluan'),
            'no_permintaan' => $this->request->getPost('no_permintaan')
        ];

        $stokData = ['status' => 'terdistribusi'];

        if ($this->distribusiModel->save($data) && $this->stokModel->update($this->request->getPost('id_bag'), $stokData)) {
            session()->setFlashdata('success', 'Distribusi darah berhasil dicatat');
        } else {
            session()->setFlashdata('error', 'Gagal mencatat distribusi darah');
        }

        return redirect()->to('/distribusi');
    }
}
