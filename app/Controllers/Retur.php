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
        $rumahSakit = $this->rumahSakitModel->findAll();

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
            'iddistribusi' => 'required',
            'idbag' => 'required',
            'idrs' => 'required',
            'tanggal_retur' => 'required',
            'alasan_return' => 'required',
            'kondisi_darah' => 'required',
            'ditangani_oleh' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'iddistribusi' => $this->request->getPost('iddistribusi'),
            'idbag' => $this->request->getPost('idbag'),
            'idrs' => $this->request->getPost('idrs'),
            'tanggal_retur' => $this->request->getPost('tanggal_retur'),
            'alasan_return' => $this->request->getPost('alasan_return'),
            'kondisi_darah' => $this->request->getPost('kondisi_darah'),
            'ditangani_oleh' => $this->request->getPost('ditangani_oleh'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        // Tentukan status stok berdasarkan kondisi darah
        $statusStok = $this->request->getPost('kondisi_darah') == 'baik' ? 'tersedia' : 'musnah';

        $stokData = [
            'status' => $statusStok
        ];

        try {
            // Mulai transaction
            $db = \Config\Database::connect();
            $db->transStart();

            // Simpan data return
            $returnSaved = $this->returnModel->save($data);
            
            // Update status stok
            $stokUpdated = $this->stokModel->update($this->request->getPost('idbag'), $stokData);

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

    public function getDistribusiInfo($iddistribusi)
    {
        $distribusi = $this->distribusiModel->select('distribusi.*, stok.no_kantong, stok.goldar, stok.jenisdarah, stok.tanggal_expired, rumah_sakit.nama_rs')
                                           ->join('stok', 'stok.idbag = distribusi.idbag')
                                           ->join('rumah_sakit', 'rumah_sakit.idrs = distribusi.idrs')
                                           ->where('distribusi.iddistribusi', $iddistribusi)
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