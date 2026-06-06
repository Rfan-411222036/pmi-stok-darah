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
        $this->stokModel       = new StokModel();
        $this->rumahSakitModel = new RumahSakitModel();
        $this->produsenModel   = new ProdusenModel();
    }

    public function index()
    {
        $search  = $this->request->getGet('search');
        $perPage = 10;

        $result = $this->distribusiModel->getDistribusiWithDetails($search, $perPage);

        return view('distribusi/index', [
            'title'      => 'Management Distribusi',
            'page_title' => 'Data Distribusi Darah',
            'distribusi' => $result['distribusi'],
            'pager'      => $result['pager'],
            'search'     => $search,
        ]);
    }

    public function create()
    {
        $rumahSakit = $this->rumahSakitModel->where('is_active', 1)->findAll();

        return view('distribusi/create', [
            'title'       => 'Tambah Distribusi',
            'page_title'  => 'Tambah Distribusi Darah',
            'rumah_sakit' => $rumahSakit,
            'validation'  => \Config\Services::validation(),
        ]);
    }

    /**
     * AJAX endpoint: Failover routing.
     *
     * 1. Resolve hospital's primary BDRS.
     * 2. Check available stock at primary BDRS for the requested blood type.
     * 3. If stock = 0, iterate remaining BDRS nodes by priority_order (failover).
     * 4. Return available bags + source BDRS + failover flag as JSON.
     */
    public function checkAvailability()
    {
        $id_rs       = (int) $this->request->getGet('id_rs');
        $gol_dar     = $this->request->getGet('gol_dar');
        $rhesus      = $this->request->getGet('rhesus');
        $jenis_darah = $this->request->getGet('jenis_darah');

        if (!$id_rs || !$gol_dar || !$rhesus || !$jenis_darah) {
            return $this->response->setJSON(['error' => 'Parameter tidak lengkap.']);
        }

        $rs = $this->rumahSakitModel->find($id_rs);
        if (!$rs) {
            return $this->response->setJSON(['error' => 'Rumah sakit tidak ditemukan.']);
        }

        $primaryBdrsId = $rs['id_primary_bdrs'] ?? null;
        $failover      = false;
        $sourceBdrs    = null;
        $bags          = [];

        // Try primary BDRS first
        if ($primaryBdrsId) {
            $bags = $this->getAvailableBags($primaryBdrsId, $gol_dar, $rhesus, $jenis_darah);
            if (!empty($bags)) {
                $sourceBdrs = $this->produsenModel->find($primaryBdrsId);
            }
        }

        // Failover: check other BDRS nodes by priority_order
        if (empty($bags)) {
            $failover = true;
            $otherNodes = $this->produsenModel
                ->where('is_active', 1)
                ->where('is_central_hub', 0)
                ->where('id_produsen !=', $primaryBdrsId ?? 0)
                ->orderBy('priority_order', 'ASC')
                ->findAll();

            foreach ($otherNodes as $node) {
                $bags = $this->getAvailableBags($node['id_produsen'], $gol_dar, $rhesus, $jenis_darah);
                if (!empty($bags)) {
                    $sourceBdrs = $node;
                    break;
                }
            }
        }

        return $this->response->setJSON([
            'failover'     => $failover,
            'primary_bdrs' => $primaryBdrsId ? $this->produsenModel->find($primaryBdrsId) : null,
            'source_bdrs'  => $sourceBdrs,
            'bags'         => $bags,
            'count'        => count($bags),
        ]);
    }

    private function getAvailableBags($id_produsen, $gol_dar, $rhesus, $jenis_darah)
    {
        return $this->stokModel
            ->where('id_produsen', $id_produsen)
            ->where('gol_dar', $gol_dar)
            ->where('rhesus', $rhesus)
            ->where('jenis_darah', $jenis_darah)
            ->where('status', 'tersedia')
            ->where('tanggal_expired >=', date('Y-m-d'))
            ->orderBy('tanggal_expired', 'ASC')
            ->findAll();
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_bag'             => 'required',
            'id_rs'              => 'required',
            'penerima'           => 'required',
            'tanggal_distribusi' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'id_bag'             => $this->request->getPost('id_bag'),
            'id_rs'              => $this->request->getPost('id_rs'),
            'tanggal_distribusi' => $this->request->getPost('tanggal_distribusi'),
            'penerima'           => $this->request->getPost('penerima'),
            'keperluan'          => $this->request->getPost('keperluan'),
            'no_permintaan'      => $this->request->getPost('no_permintaan'),
        ];

        if ($this->distribusiModel->save($data) && $this->stokModel->update($data['id_bag'], ['status' => 'terdistribusi'])) {
            session()->setFlashdata('success', 'Distribusi darah berhasil dicatat.');
        } else {
            session()->setFlashdata('error', 'Gagal mencatat distribusi darah.');
        }

        return redirect()->to('/distribusi');
    }
}
