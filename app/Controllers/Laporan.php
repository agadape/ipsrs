<?php

namespace App\Controllers;

use App\Config\IPSRS;
use App\Models\LKModel;
use App\Models\StokModel;
use App\Models\JadwalModel;

class Laporan extends BaseController
{
    private function getData(string $period): array
    {
        $lkModel     = new LKModel();
        $stokModel   = new StokModel();
        $jadwalModel = new JadwalModel();

        $allLK     = $lkModel->getAll();
        $allStok   = $stokModel->getAll();
        $allJadwal = $jadwalModel->getAll();

        $today     = date('Y-m-d');
        $thisMonth = date('Y-m');
        $thisYear  = date('Y');

        $filteredLK = match($period) {
            'minggu' => array_filter($allLK, fn($l) =>
                $l['tanggal'] >= date('Y-m-d', strtotime('-7 days'))),
            'tahun'  => array_filter($allLK, fn($l) =>
                str_starts_with($l['tanggal'], $thisYear)),
            default  => array_filter($allLK, fn($l) =>
                str_starts_with($l['tanggal'], $thisMonth)),
        };
        $filteredLK = array_values($filteredLK);

        $totalLK   = count($filteredLK);
        $selesai   = count(array_filter($filteredLK, fn($l) => $l['status'] === IPSRS::STATUS_LK[6]));
        $aktif     = count(array_filter($filteredLK, fn($l) => $l['status'] !== IPSRS::STATUS_LK[6]));

        $lkWithRT  = array_filter($filteredLK, fn($l) => $l['response_time'] !== null);
        $slaPct    = count($lkWithRT) > 0
            ? round(count(array_filter($lkWithRT, fn($l) => $l['response_time'] <= IPSRS::SLA_RESPONSE_TIME)) / count($lkWithRT) * 100)
            : 0;
        $avgRespon = count($lkWithRT) > 0
            ? round(array_sum(array_column(array_values($lkWithRT), 'response_time')) / count($lkWithRT))
            : 0;

        $stokHabis   = count(array_filter($allStok, fn($s) => $s['stok_tersedia'] <= 0));
        $stokMenipis = count(array_filter($allStok, fn($s) =>
            $s['stok_tersedia'] > 0 && $s['stok_tersedia'] <= $s['minimum_stok']));

        $jadwalBulan   = array_filter($allJadwal, fn($j) => str_starts_with($j['tanggal'], $thisMonth));
        $jadwalSelesai = count(array_filter($jadwalBulan, fn($j) => $j['status'] === IPSRS::STATUS_JADWAL[1]));
        $jadwalTotal   = count($jadwalBulan);
        $pmPct         = $jadwalTotal > 0 ? round($jadwalSelesai / $jadwalTotal * 100) : 0;

        $kodeGroups = [];
        foreach ($filteredLK as $l) {
            $kodeGroups[$l['kode']] = ($kodeGroups[$l['kode']] ?? 0) + 1;
        }

        return compact(
            'filteredLK', 'allStok', 'allJadwal',
            'totalLK', 'selesai', 'aktif', 'slaPct', 'avgRespon',
            'stokHabis', 'stokMenipis', 'jadwalSelesai', 'jadwalTotal', 'pmPct',
            'kodeGroups'
        );
    }

    public function index(): string
    {
        $period = $this->request->getGet('period') ?? 'bulan';
        $data   = $this->getData($period);
        return $this->render('pages/laporan/index', array_merge($data, ['period' => $period]));
    }

    public function exportCsv()
    {
        $period     = $this->request->getGet('period') ?? 'bulan';
        $data       = $this->getData($period);
        $filteredLK = $data['filteredLK'];

        $rows = [['No. Order','Tanggal','Jam','Pelapor','Unit Pelapor','Lokasi','Keluhan','Kode','Status','Teknisi','Tindakan','Resp. Time (mnt)','Down Time (mnt)']];
        foreach ($filteredLK as $l) {
            $rows[] = [
                $l['no_order']     ?? '',
                $l['tanggal']      ?? '',
                $l['jam_laporan']  ?? '',
                $l['pelapor']      ?? '',
                $l['unit_pelapor'] ?? '',
                $l['lokasi']       ?? '',
                $l['keluhan']      ?? '',
                $l['kode']         ?? '',
                $l['status']       ?? '',
                $l['teknisi']      ?? '',
                $l['tindakan']     ?? '',
                $l['response_time'] ?? '',
                $l['down_time']    ?? '',
            ];
        }

        $out = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        $filename = 'Laporan_LK_' . date('Y-m-d') . '.csv';
        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody("\xEF\xBB\xBF" . $csv); // UTF-8 BOM for Excel
    }

    public function exportPrint()
    {
        $period = $this->request->getGet('period') ?? 'bulan';
        $data   = $this->getData($period);
        $periodLabels = ['minggu' => 'Minggu Ini', 'bulan' => 'Bulan Ini', 'tahun' => 'Tahun Ini'];
        $data['period']      = $period;
        $data['periodLabel'] = $periodLabels[$period] ?? 'Bulan Ini';
        
        $lkModel   = new \App\Models\LKModel();
        $asetModel = new \App\Models\AsetModel();
        
        foreach ($data['filteredLK'] as &$lk) {
            $aset = $asetModel->find($lk['id_aset']);
            $lk['nama_aset'] = $aset['nama'] ?? '-';
            
            $scList = $lkModel->getSukuCadang($lk['id']);
            if (empty($scList)) {
                $lk['suku_cadang_str'] = '-';
            } else {
                $lk['suku_cadang_str'] = implode(', ', array_map(fn($sc) => ($sc['nama_barang'] ?? 'Unknown') . ' (' . $sc['jumlah'] . ')', $scList));
            }
        }
        
        return view('pages/laporan/print', $data);
    }

    public function exportPrintPreventif()
    {
        $period = $this->request->getGet('period') ?? 'bulan';
        
        $lkpModel = new \App\Models\LkpModel();
        $allLKP   = $lkpModel->getAll();
        
        $thisMonth = date('Y-m');
        $thisYear  = date('Y');

        $filtered = match($period) {
            'minggu' => array_filter($allLKP, fn($l) => $l['tanggal_pemeriksaan'] >= date('Y-m-d', strtotime('-7 days'))),
            'tahun'  => array_filter($allLKP, fn($l) => str_starts_with($l['tanggal_pemeriksaan'], $thisYear)),
            default  => array_filter($allLKP, fn($l) => str_starts_with($l['tanggal_pemeriksaan'], $thisMonth)),
        };
        
        $asetModel   = new \App\Models\AsetModel();
        $jadwalModel = new \App\Models\JadwalModel();
        
        $dataLKP = [];
        foreach ($filtered as $lkp) {
            $aset   = !empty($lkp['id_aset']) ? $asetModel->getById($lkp['id_aset']) : null;
            $jadwal = !empty($lkp['id_jadwal']) ? $jadwalModel->getById($lkp['id_jadwal']) : null;
            
            $dataLKP[] = [
                'nama_unit' => $aset['nama'] ?? $jadwal['aset'] ?? '-',
                'lokasi'    => $jadwal['lokasi'] ?? $aset['lokasi'] ?? '-',
                'no_seri'   => $aset['nomor_seri'] ?? '-',
                'tanggal'   => $lkp['tanggal_pemeriksaan'] ?? '-',
                'hasil'     => $lkp['hasil_pemeriksaan'] ?? '-',
                'temuan'    => $lkp['catatan'] ?? '-',
                'keterangan'=> '-', // Kosong untuk keterangan nl
            ];
        }

        $periodLabels = ['minggu' => 'Minggu Ini', 'bulan' => 'Bulan Ini', 'tahun' => 'Tahun Ini'];
        
        return view('pages/laporan/print_preventif', [
            'periodLabel' => $periodLabels[$period] ?? 'Bulan Ini',
            'dataLKP'     => $dataLKP
        ]);
    }

    public function exportExcelPreventif()
    {
        $period = $this->request->getGet('period') ?? 'bulan';
        
        $lkpModel = new \App\Models\LkpModel();
        $allLKP   = $lkpModel->getAll();
        
        $thisMonth = date('Y-m');
        $thisYear  = date('Y');

        $filtered = match($period) {
            'minggu' => array_filter($allLKP, fn($l) => $l['tanggal_pemeriksaan'] >= date('Y-m-d', strtotime('-7 days'))),
            'tahun'  => array_filter($allLKP, fn($l) => str_starts_with($l['tanggal_pemeriksaan'], $thisYear)),
            default  => array_filter($allLKP, fn($l) => str_starts_with($l['tanggal_pemeriksaan'], $thisMonth)),
        };
        
        $asetModel   = new \App\Models\AsetModel();
        $jadwalModel = new \App\Models\JadwalModel();
        
        $periodLabels = ['minggu' => 'Minggu Ini', 'bulan' => 'Bulan Ini', 'tahun' => 'Tahun Ini'];
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title (Rows 1-3)
        $sheet->setCellValue('A1', 'Rekapitulasi Hasil Preventif');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A2', 'RSUD Kota Yogyakarta');
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A3', 'Tahun ' . $thisYear);
        $sheet->mergeCells('A3:K3');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
        
        // Metadata (Rows 4-5)
        $sheet->setCellValue('A4', 'Rentang Preventif');
        $sheet->setCellValue('C4', '1 Bulanan');
        $sheet->setCellValue('A5', 'Periode');
        $sheet->setCellValue('C5', $periodLabels[$period] ?? 'Bulan Ini');
        
        // Headers (Rows 6-7)
        $headers = ['No', 'Unit', 'No. ID / Seri', 'Lokasi', 'Rencana Pelaksanaan', 'Pelaksanaan', 'Hasil Pemeriksaan', 'Temuan / Rekomendasi Awal', 'Rencana Perbaikan', 'Keterangan', 'Kesesuaian Rencana dengan Pelaksanaan'];
        foreach (range('A', 'K') as $i => $col) {
            $sheet->setCellValue($col . '6', $headers[$i]);
            if ($col !== 'K') {
                $sheet->mergeCells($col . '6:' . $col . '7');
            }
        }
        $sheet->setCellValue('K7', 'Ya / Tidak');
        
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A6:K7')->applyFromArray($headerStyle);
        
        // Data
        $row = 8;
        $no = 1;
        foreach ($filtered as $lkp) {
            $aset   = !empty($lkp['id_aset']) ? $asetModel->getById($lkp['id_aset']) : null;
            $jadwal = !empty($lkp['id_jadwal']) ? $jadwalModel->getById($lkp['id_jadwal']) : null;
            
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $aset['nama'] ?? $jadwal['aset'] ?? '-');
            $sheet->setCellValue('C' . $row, $aset['nomor_seri'] ?? '-');
            $sheet->setCellValue('D' . $row, $jadwal['lokasi'] ?? $aset['lokasi'] ?? '-');
            $sheet->setCellValue('E' . $row, ''); // Rencana
            $sheet->setCellValue('F' . $row, $lkp['tanggal_pemeriksaan'] ?? '-');
            $sheet->setCellValue('G' . $row, $lkp['hasil_pemeriksaan'] ?? '-');
            $sheet->setCellValue('H' . $row, $lkp['catatan'] ?? '-');
            $sheet->setCellValue('I' . $row, '-');
            $sheet->setCellValue('J' . $row, '-');
            $sheet->setCellValue('K' . $row, '');
            $row++;
        }
        
        if ($row > 8) {
            $sheet->getStyle('A8:K' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                'alignment' => ['vertical' => 'top', 'wrapText' => true]
            ]);
        }
        
        $cols = ['A'=>5, 'B'=>20, 'C'=>15, 'D'=>15, 'E'=>15, 'F'=>15, 'G'=>15, 'H'=>30, 'I'=>20, 'J'=>15, 'K'=>15];
        foreach ($cols as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }
        
        $filename = 'Laporan_Preventif_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
