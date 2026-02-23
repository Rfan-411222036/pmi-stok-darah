<?php

namespace App\Controllers;

use App\Models\StokModel;
use App\Models\ProdusenModel;
use App\Models\RumahSakitModel;
use App\Models\DistribusiModel;
use App\Models\PemusnahanModel;
use App\Models\UserModel;
use App\Libraries\PdfGenerator;

class Dashboard extends BaseController
{
    protected $stokModel;
    protected $produsenModel;
    protected $rsModel;
    protected $distribusiModel;
    protected $pemusnahanModel;
    protected $userModel;

    public function __construct()
    {
        $this->stokModel = new StokModel();
        $this->produsenModel = new ProdusenModel();
        $this->rsModel = new RumahSakitModel();
        $this->distribusiModel = new DistribusiModel();
        $this->pemusnahanModel = new PemusnahanModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Data untuk statistik cards
        $totalStok = $this->stokModel->getStokTersedia();
        $totalProdusen = $this->produsenModel->countAll();
        $totalRS = $this->rsModel->countAll();
        $totalDistribusi = $this->distribusiModel->getTotalDistribusi();
        $totalPemusnahan = $this->pemusnahanModel->getTotalPemusnahan();
        $stokExpired = $this->stokModel->getStokExpired();
        $stokMendekatiExpired = $this->stokModel->getStokMendekatiExpired();
        $distribusiHariIni = $this->distribusiModel->getDistribusiHariIni();
        $distribusiBulanIni = $this->distribusiModel->getDistribusiBulanIni();

        // Data untuk charts
        $stokByGolongan = $this->stokModel->getStokByGolongan();
        $stokByJenis = $this->stokModel->getStokByJenis();

        // Data statistik user
        $totalUsers = $this->userModel->getTotalUsers();
        $totalAdmins = $this->userModel->getTotalAdmins();
        $totalStaff = $this->userModel->getTotalStaff();

        // Data aktivitas terbaru
        $recentDistribusi = $this->distribusiModel->getRecentDistribusi(5);

        $data = [
            'title' => 'Dashboard',
            'page_title' => 'Dashboard Management Stok Darah PMI',
            'total_stok' => $totalStok,
            'total_produsen' => $totalProdusen,
            'total_rs' => $totalRS,
            'total_distribusi' => $totalDistribusi,
            'total_pemusnahan' => $totalPemusnahan,
            'distribusi_hari_ini' => $distribusiHariIni,
            'distribusi_bulan_ini' => $distribusiBulanIni,
            'stok_expired' => $stokExpired,
            'stok_mendekati_expired' => $stokMendekatiExpired,
            'stok_by_golongan' => $stokByGolongan,
            'stok_by_jenis' => $stokByJenis,
            'total_users' => $totalUsers,
            'total_admins' => $totalAdmins,
            'total_staff' => $totalStaff,
            'recent_distribusi' => $recentDistribusi,
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Download laporan stok PDF
     */
    public function downloadLaporan()
    {
        $stokData = $this->stokModel->select('idbag, no_kantong, jenisdarah, goldar, rhesus, volume, tanggal_produksi, tanggal_expired, status')
                                     ->findAll();

        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN STOK DARAH', date('d F Y'));

        $pdf->addTitle('LAPORAN STOK DARAH');
        $pdf->Ln(3);

        // Statistik
        $stats = [
            ['label' => 'Total Unit', 'value' => count($stokData)],
        ];
        $pdf->addStats($stats);

        if (empty($stokData)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data stok darah.', 0, 1, 'C');
        } else {
            // Tabel
            $headers = ['No', 'No. Kantong', 'Jenis', 'Goldar', 'Volume', 'Expired Date'];
            $tableData = [];

            $no = 1;
            foreach ($stokData as $item) {
                $tableData[] = [
                    $no++,
                    $item['no_kantong'] ?? '-',
                    ($item['jenisdarah'] ?? '-') . ' ' . ($item['goldar'] ?? '-') . ($item['rhesus'] ?? '+'),
                    $item['goldar'] ?? '-',
                    ($item['volume'] ?? '-') . ' ml',
                    $item['tanggal_expired'] ?? '-',
                ];
            }

            $columnWidths = [8, 25, 25, 15, 20, 27];
            $pdf->addTable($headers, $tableData, $columnWidths);
        }

        $pdf->Output('Laporan_Stok_' . date('d-m-Y') . '.pdf', 'D');
    }

    /**
     * Download laporan distribusi PDF
     */
    public function downloadDistribusi()
    {
        $distribusiData = $this->distribusiModel->select('iddistribusi, idbag, idrs, tanggal_distribusi, penerima, keperluan')
                                                ->findAll();

        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN DISTRIBUSI DARAH', date('d F Y'));

        $pdf->addTitle('LAPORAN DISTRIBUSI DARAH');
        $pdf->Ln(3);

        // Statistik
        $stats = [
            ['label' => 'Total Transaksi', 'value' => count($distribusiData)],
        ];
        $pdf->addStats($stats);

        if (empty($distribusiData)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data distribusi.', 0, 1, 'C');
        } else {
            // Tabel (tambahkan kolom No. Kantong)
            $headers = ['No', 'No. Kantong', 'Tanggal', 'Penerima', 'Keperluan', 'Rumah Sakit'];
            $tableData = [];

            $no = 1;
            foreach ($distribusiData as $item) {
                // Get RS name
                $rs = $this->rsModel->find($item['idrs']);
                $rsName = $rs['nama_rs'] ?? '-';

                // Get bag (stok) info to retrieve no_kantong
                $bag = $this->stokModel->find($item['idbag']);
                $noKantong = $bag['no_kantong'] ?? '-';
                
                $tableData[] = [
                    $no++,
                    $noKantong,
                    date('d-m-Y', strtotime($item['tanggal_distribusi'] ?? 'now')),
                    $item['penerima'] ?? '-',
                    substr($item['keperluan'] ?? '-', 0, 15),
                    $rsName,
                ];
            }

            $columnWidths = [8, 30, 18, 25, 25, 34];
            $pdf->addTable($headers, $tableData, $columnWidths);
        }

        $pdf->Output('Laporan_Distribusi_' . date('d-m-Y') . '.pdf', 'D');
    }

    /**
     * Download laporan pemusnahan PDF
     */
    public function downloadPemusnahan()
    {
        $pemusnahanData = $this->pemusnahanModel->select('idpemusnahan, idbag, tanggal_pemusnahan, alasan, keterangan, petugas')
                                                ->findAll();

        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN PEMUSNAHAN DARAH', date('d F Y'));

        $pdf->addTitle('LAPORAN PEMUSNAHAN DARAH');
        $pdf->Ln(3);

        // Statistik
        $stats = [
            ['label' => 'Total Pemusnahan', 'value' => count($pemusnahanData)],
        ];
        $pdf->addStats($stats);

        if (empty($pemusnahanData)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data pemusnahan.', 0, 1, 'C');
        } else {
            // Tabel (tambahkan kolom No. Kantong)
            $headers = ['No', 'No. Kantong', 'Tanggal', 'Alasan', 'Petugas', 'Keterangan'];
            $tableData = [];

            $no = 1;
            foreach ($pemusnahanData as $item) {
                // Get bag (stok) info to retrieve no_kantong
                $bag = $this->stokModel->find($item['idbag']);
                $noKantong = $bag['no_kantong'] ?? '-';

                $tableData[] = [
                    $no++,
                    $noKantong,
                    date('d-m-Y', strtotime($item['tanggal_pemusnahan'] ?? 'now')),
                    $item['alasan'] ?? '-',
                    $item['petugas'] ?? '-',
                    substr($item['keterangan'] ?? '-', 0, 20),
                ];
            }

            $columnWidths = [8, 30, 18, 20, 25, 39];
            $pdf->addTable($headers, $tableData, $columnWidths);
        }

        $pdf->Output('Laporan_Pemusnahan_' . date('d-m-Y') . '.pdf', 'D');
    }

    /**
     * Download laporan retur PDF
     */
    public function downloadRetur()
    {
        $returnModel = new \App\Models\ReturnModel();
        // Join stok to get no_kantong for each retur
        $returData = $returnModel->select('return_darah.idreturn, return_darah.tanggal_retur, return_darah.alasan_return, return_darah.kondisi_darah, return_darah.ditangani_oleh, stok.no_kantong')
                                 ->join('stok', 'stok.idbag = return_darah.idbag')
                                 ->findAll();

        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN RETUR DARAH', date('d F Y'));

        $pdf->addTitle('LAPORAN RETUR DARAH');
        $pdf->Ln(3);

        // Statistik
        $stats = [
            ['label' => 'Total Retur', 'value' => count($returData)],
        ];
        $pdf->addStats($stats);

        if (empty($returData)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data retur.', 0, 1, 'C');
        } else {
            // Tabel (tambahkan kolom No. Kantong)
            $headers = ['No', 'No. Kantong', 'Tanggal', 'Alasan Retur', 'Kondisi', 'Ditangani Oleh'];
            $tableData = [];

            $no = 1;
            foreach ($returData as $item) {
                $tableData[] = [
                    $no++,
                    $item['no_kantong'] ?? '-',
                    date('d-m-Y', strtotime($item['tanggal_retur'] ?? 'now')),
                    $item['alasan_return'] ?? '-',
                    $item['kondisi_darah'] ?? '-',
                    $item['ditangani_oleh'] ?? '-',
                ];
            }

            $columnWidths = [8, 30, 18, 28, 18, 28];
            $pdf->addTable($headers, $tableData, $columnWidths);
        }

        $pdf->Output('Laporan_Retur_' . date('d-m-Y') . '.pdf', 'D');
    }
}