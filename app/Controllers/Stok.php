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
        $produsenFilter = $this->request->getGet('id_produsen');
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');
        $keterangan = $this->request->getGet('keterangan');
        $perPage = 10;
        $role = session()->get('role');
        $userId = session()->get('id_user');
        $produsenName = null;

        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser($userId);
            if ($ownProdusen) {
                $produsenFilter = $ownProdusen['id_produsen'];
                $produsenName = $ownProdusen['nama'];
            } else {
                $produsenFilter = null;
            }
        }

        if ($role === 'rs') {
            $result = [
                'stok' => [],
                'pager' => $this->stokModel->pager
            ];
            $golonganRhesus = [];
        } else {
            $result = $this->stokModel->getStokWithDetails($search, $perPage, $produsenFilter, $from, $to, $keterangan);
            $golonganRhesus = $this->stokModel->getStokByGolonganRhesus($produsenFilter);
        }

        $result = $this->stokModel->getStokWithDetails($search, $perPage, $produsenFilter, $from, $to, $keterangan);
        $golonganRhesus = $this->stokModel->getStokByGolonganRhesus($produsenFilter);

        $bloodGroups = ['A', 'B', 'AB', 'O'];
        $rhesusCounts = [
            '+' => array_fill_keys($bloodGroups, 0),
            '-' => array_fill_keys($bloodGroups, 0),
        ];

        foreach ($golonganRhesus as $item) {
            $group = $item['gol_dar'];
            $rhesus = $item['rhesus'] ?? '+';

            if (isset($rhesusCounts[$rhesus][$group])) {
                $rhesusCounts[$rhesus][$group] = (int) $item['total'];
            }
        }

        if (!$produsenName && $produsenFilter) {
            $produsen = $this->produsenModel->find($produsenFilter);
            $produsenName = $produsen ? $produsen['nama'] : null;
        }

        $data = [
            'title' => 'Management Stok Darah',
            'page_title' => 'Data Stok Darah',
            'stok' => $result['stok'] ?? [],
            'pager' => $result['pager'] ?? $this->stokModel->pager,
            'search' => $search,
            'filter_produsen' => $produsenFilter,
            'filter_produsen_name' => $produsenName,
            'from' => $from,
            'to' => $to,
            'keterangan' => $keterangan,
            'keteranganOptions' => $this->stokModel->getDistinctKeterangan(),
            'chartLabels' => $bloodGroups,
            'chartDataPlus' => array_values($rhesusCounts['+']),
            'chartDataMinus' => array_values($rhesusCounts['-']),
            'stockAvailable' => $this->stokModel->getStokTersedia($produsenFilter),
            'stockNearExpire' => $this->stokModel->getStokMendekatiExpired($produsenFilter),
            'stockExpired' => $this->stokModel->getStokExpired($produsenFilter),
        ];

        return view('stok/index', $data);
    }

    public function downloadReport()
    {
        helper('url');

        $search = $this->request->getGet('search');
        $produsenFilter = $this->request->getGet('id_produsen');
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');
        $keterangan = $this->request->getGet('keterangan');

        $stokData = $this->stokModel->getStokWithDetails($search, null, $produsenFilter, $from, $to, $keterangan)['stok'];

        $pdf = new \App\Libraries\PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN STOK DARAH', date('d F Y'));
        $pdf->addTitle('LAPORAN STOK DARAH');

        $meta = [];
        if ($from || $to) {
            $meta[] = 'Periode: ' . ($from ?: '-') . ' s/d ' . ($to ?: '-');
        }
        if ($keterangan) {
            $meta[] = 'Keterangan: ' . $keterangan;
        }
        if ($search) {
            $meta[] = 'Search: ' . $search;
        }

        if (!empty($meta)) {
            foreach ($meta as $line) {
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(0, 6, $line, 0, 1, 'L');
            }
            $pdf->Ln(2);
        }

        if (empty($stokData)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data stok sesuai filter.', 0, 1, 'C');
            $pdf->Output('Laporan_Stok_' . date('d-m-Y') . '.pdf', 'D');
            return;
        }

        $headers = ['No', 'No. Kantong', 'BDRS', 'Gol', 'Rhesus', 'Jenis', 'Volume', 'Produksi', 'Expired', 'Status', 'Keterangan'];
        $tableData = [];
        $no = 1;
        foreach ($stokData as $item) {
            $tableData[] = [
                $no++,
                $item['no_kantong'] ?? '-',
                $item['nama_produsen'] ?? '-',
                $item['gol_dar'] ?? '-',
                $item['rhesus'] ?? '-',
                $item['jenis_darah'] ?? '-',
                ($item['volume'] ?? '-') . ' ml',
                $item['tanggal_produksi'] ?? '-',
                $item['tanggal_expired'] ?? '-',
                ucfirst($item['status'] ?? '-'),
                $item['keterangan'] ?? '-',
            ];
        }

        $columnWidths = [8, 25, 30, 12, 12, 20, 18, 20, 20, 18, 35];
        $pdf->addTable($headers, $tableData, $columnWidths);
        $pdf->Output('Laporan_Stok_' . date('d-m-Y') . '.pdf', 'D');
    }

    public function create()
    {
        $role = session()->get('role');
        $produsen = [];

        if ($role === 'rs') {
            session()->setFlashdata('error', 'Akses tidak diizinkan.');
            return redirect()->to('/stok');
        }

        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser(session()->get('id_user'));
            if ($ownProdusen) {
                $produsen = [$ownProdusen];
            }
        } else {
            $produsen = $this->produsenModel->findAll();
        }

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
        $role = session()->get('role');

        if ($role === 'rs') {
            session()->setFlashdata('error', 'Akses tidak diizinkan.');
            return redirect()->to('/stok');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'no_kantong' => 'required|is_unique[stok.no_kantong]',
            'id_produsen' => 'required',
            'jenis_darah' => 'required',
            'gol_dar' => 'required',
            'volume' => 'required|numeric',
            'tanggal_produksi' => 'required',
            'tanggal_expired' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $idProdusen = $this->request->getPost('id_produsen');
        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser(session()->get('id_user'));
            if (!$ownProdusen || $ownProdusen['id_produsen'] != $idProdusen) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/stok');
            }
        }

        $data = [
            'no_kantong' => $this->request->getPost('no_kantong'),
            'id_produsen' => $idProdusen,
            'jenis_darah' => $this->request->getPost('jenis_darah'),
            'gol_dar' => $this->request->getPost('gol_dar'),
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
        $role = session()->get('role');
        $stok = $this->stokModel->find($id);

        if (!$stok) {
            session()->setFlashdata('error', 'Data stok tidak ditemukan');
            return redirect()->to('/stok');
        }

        if ($role === 'rs') {
            session()->setFlashdata('error', 'Akses tidak diizinkan.');
            return redirect()->to('/stok');
        }

        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser(session()->get('id_user'));
            if (!$ownProdusen || $stok['id_produsen'] != $ownProdusen['id_produsen']) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/stok');
            }
            $produsen = [$ownProdusen];
        } else {
            $produsen = $this->produsenModel->findAll();
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
        $role = session()->get('role');
        $stok = $this->stokModel->find($id);

        if (!$stok) {
            session()->setFlashdata('error', 'Data stok tidak ditemukan');
            return redirect()->to('/stok');
        }

        if ($role === 'rs') {
            session()->setFlashdata('error', 'Akses tidak diizinkan.');
            return redirect()->to('/stok');
        }

        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser(session()->get('id_user'));
            if (!$ownProdusen || $stok['id_produsen'] != $ownProdusen['id_produsen']) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/stok');
            }
        }

        $noKantongRules = $stok['no_kantong'] === $this->request->getPost('no_kantong') ?
            'required' : 'required|is_unique[stok.no_kantong]';

        $validation = \Config\Services::validation();
        $validation->setRules([
            'no_kantong' => $noKantongRules,
            'id_produsen' => 'required',
            'jenis_darah' => 'required',
            'gol_dar' => 'required',
            'volume' => 'required|numeric',
            'tanggal_produksi' => 'required',
            'tanggal_expired' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $idProdusen = $this->request->getPost('id_produsen');
        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser(session()->get('id_user'));
            if (!$ownProdusen || $ownProdusen['id_produsen'] != $idProdusen) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/stok');
            }
        }

        $data = [
            'no_kantong' => $this->request->getPost('no_kantong'),
            'id_produsen' => $idProdusen,
            'jenis_darah' => $this->request->getPost('jenis_darah'),
            'gol_dar' => $this->request->getPost('gol_dar'),
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
        $role = session()->get('role');
        $stok = $this->stokModel->find($id);

        if (!$stok) {
            session()->setFlashdata('error', 'Data stok tidak ditemukan');
            return redirect()->to('/stok');
        }

        if ($role === 'rs') {
            session()->setFlashdata('error', 'Akses tidak diizinkan.');
            return redirect()->to('/stok');
        }

        if ($role === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser(session()->get('id_user'));
            if (!$ownProdusen || $stok['id_produsen'] != $ownProdusen['id_produsen']) {
                session()->setFlashdata('error', 'Akses tidak diizinkan.');
                return redirect()->to('/stok');
            }
        }

        if ($stok['status'] === 'terdistribusi') {
            session()->setFlashdata('error', 'Tidak dapat menghapus stok yang sudah didistribusikan');
            return redirect()->to('/stok');
        }

        if ($stok['status'] === 'musnah') {
            session()->setFlashdata('error', 'Tidak dapat menghapus stok yang sudah dimusnahkan');
            return redirect()->to('/stok');
        }

        $distribusiModel = new \App\Models\DistribusiModel();
        $distribusiCount = $distribusiModel->where('id_bag', $id)->countAllResults();

        if ($distribusiCount > 0) {
            session()->setFlashdata('error', 'Tidak dapat menghapus stok karena sudah didistribusikan');
            return redirect()->to('/stok');
        }

        $pemusnahanModel = new \App\Models\PemusnahanModel();
        $pemusnahanCount = $pemusnahanModel->where('id_bag', $id)->countAllResults();

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
