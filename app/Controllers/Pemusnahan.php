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
        // Ambil stok yang expired atau status tersedia (untuk kasus rusak)
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
            'idbag' => 'required',
            'tanggal_pemusnahan' => 'required',
            'alasan' => 'required',
            'petugas' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'idbag' => $this->request->getPost('idbag'),
            'tanggal_pemusnahan' => $this->request->getPost('tanggal_pemusnahan'),
            'alasan' => $this->request->getPost('alasan'),
            'keterangan' => $this->request->getPost('keterangan'),
            'petugas' => $this->request->getPost('petugas')
        ];

        // Update status stok menjadi musnah
        $stokData = [
            'status' => 'musnah'
        ];

        try {
            // Mulai transaction
            $db = \Config\Database::connect();
            $db->transStart();

            // Simpan data pemusnahan
            $pemusnahanSaved = $this->pemusnahanModel->save($data);
            
            // Update status stok
            $stokUpdated = $this->stokModel->update($this->request->getPost('idbag'), $stokData);

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