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
                session()->setFlashdata('success', 'Data pemusnahan berhasil dicatat');
            } else {
                session()->setFlashdata('error', 'Gagal mencatat data pemusnahan');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to('/pemusnahan');
    }
}
