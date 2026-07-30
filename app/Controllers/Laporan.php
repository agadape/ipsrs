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

    public function exportExcelLK()
    {
        $period     = $this->request->getGet('period') ?? 'bulan';
        $data       = $this->getData($period);
        $filteredLK = $data['filteredLK'];
        $periodLabels = ['minggu' => 'Minggu Ini', 'bulan' => 'Bulan Ini', 'tahun' => 'Tahun Ini'];
        $periodStr = $periodLabels[$period] ?? 'Bulan Ini';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header / Title
        $sheet->setCellValue('A1', 'Laporan Rekapitulasi Kerusakan Aset');
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A2', 'RSUD Kota Yogyakarta');
        $sheet->mergeCells('A2:M2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A3', 'Periode: ' . $periodStr);
        $sheet->mergeCells('A3:M3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

        $seriesModel = new \App\Models\AsetSeriesModel();
        $asetModel = new \App\Models\AsetModel();

        // Table Header
        $headers = ['No', 'No. Order', 'Tanggal', 'Jam', 'Pelapor (Unit)', 'Aset / Unit', 'Lokasi', 'Keluhan', 'Status', 'Teknisi', 'Tindakan', 'Resp. Time', 'Down Time'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '5', $h);
            $sheet->getStyle($col . '5')->getFont()->setBold(true);
            $sheet->getStyle($col . '5')->getFill()
                  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                  ->getStartColor()->setARGB('FFD9D9D9');
            $sheet->getStyle($col . '5')->getAlignment()->setHorizontal('center');
            $col++;
        }

        // Table Data
        $row = 6;
        $no = 1;
        foreach ($filteredLK as $l) {
            $series = !empty($l['id_aset_series']) ? $seriesModel->getById($l['id_aset_series']) : null;
            $aset = $series ? $asetModel->getById($series['id_aset']) : null;
            $namaAset = $aset['nama'] ?? $l['nama_aset'] ?? '-';
            $nomorAset = $series['nomor_aset'] ?? '';
            $asetStr = $nomorAset ? "{$nomorAset} - {$namaAset}" : $namaAset;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $l['no_order'] ?? '-');
            $sheet->setCellValue('C' . $row, $l['tanggal'] ?? '-');
            $sheet->setCellValue('D' . $row, $l['jam_laporan'] ?? '-');
            $sheet->setCellValue('E' . $row, ($l['pelapor'] ?? '-') . ' (' . ($l['unit_pelapor'] ?? '-') . ')');
            $sheet->setCellValue('F' . $row, $asetStr);
            $sheet->setCellValue('G' . $row, $l['lokasi'] ?? $series['ruangan'] ?? '-');
            $sheet->setCellValue('H' . $row, $l['keluhan'] ?? '-');
            $sheet->setCellValue('I' . $row, $l['status'] ?? '-');
            $sheet->setCellValue('J' . $row, $l['teknisi'] ?? '-');
            $sheet->setCellValue('K' . $row, $l['tindakan'] ?? '-');
            $sheet->setCellValue('L' . $row, $l['response_time'] ?? '-');
            $sheet->setCellValue('M' . $row, $l['down_time'] ?? '-');
            
            $sheet->getStyle('A'.$row.':M'.$row)->getAlignment()->setVertical('top');
            $sheet->getStyle('H'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('K'.$row)->getAlignment()->setWrapText(true);
            $row++;
        }

        // Borders
        $lastCol = 'M';
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A5:' . $lastCol . $lastRow)->applyFromArray($styleArray);

        // Auto size columns (except textarea cols)
        foreach (range('A', 'M') as $colId) {
            if (!in_array($colId, ['H', 'K'])) {
                $sheet->getColumnDimension($colId)->setAutoSize(true);
            } else {
                $sheet->getColumnDimension($colId)->setWidth(40);
            }
        }

        // Signature Block
        $sigRow = $lastRow + 3;
        $sheet->setCellValue('K' . $sigRow, 'Yogyakarta, ' . date('d F Y'));
        $sheet->setCellValue('K' . ($sigRow + 1), 'Kepala IPSRS RSUD Kota YK,');
        
        $sheet->setCellValue('K' . ($sigRow + 5), \App\Config\IPSRS::NAMA_KEPALA);
        $sheet->getStyle('K' . ($sigRow + 5))->getFont()->setUnderline(true)->setBold(true);
        $sheet->setCellValue('K' . ($sigRow + 6), 'NIP. ' . \App\Config\IPSRS::NIP_KEPALA);
        
        $sheet->getStyle('K'.$sigRow.':K'.($sigRow+6))->getAlignment()->setHorizontal('center');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Laporan_Kerusakan_' . date('Y-m-d') . '.xlsx';
        
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($content);
    }

    public function exportPrint()
    {
        $period = $this->request->getGet('period') ?? 'bulan';
        $data   = $this->getData($period);
        $periodLabels = ['minggu' => 'Minggu Ini', 'bulan' => 'Bulan Ini', 'tahun' => 'Tahun Ini'];
        $data['period']      = $period;
        $data['periodLabel'] = $periodLabels[$period] ?? 'Bulan Ini';
        
        $lkModel   = new \App\Models\LKModel();
        $seriesModel = new \App\Models\AsetSeriesModel();
        $asetModel = new \App\Models\AsetModel();
        
        foreach ($data['filteredLK'] as &$lk) {
            $series = !empty($lk['id_aset_series']) ? $seriesModel->getById($lk['id_aset_series']) : null;
            $aset = $series ? $asetModel->getById($series['id_aset']) : null;
            
            $nama = $aset['nama'] ?? $lk['nama_aset'] ?? '-';
            $nomor = $series['nomor_aset'] ?? '';
            $lk['nama_aset'] = $nomor ? "{$nomor} - {$nama}" : $nama;
            
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
        
        $seriesModel = new \App\Models\AsetSeriesModel();
        $asetModel = new \App\Models\AsetModel();
        $jadwalModel = new \App\Models\JadwalModel();
        
        $dataLKP = [];
        foreach ($filtered as $lkp) {
            $series = !empty($lkp['id_aset_series']) ? $seriesModel->getById($lkp['id_aset_series']) : null;
            $aset = $series ? $asetModel->getById($series['id_aset']) : null;
            $jadwal = !empty($lkp['id_jadwal']) ? $jadwalModel->getById($lkp['id_jadwal']) : null;
            
            $dataLKP[] = [
                'nama_unit' => $aset['nama'] ?? $jadwal['aset'] ?? '-',
                'lokasi'    => $series['ruangan'] ?? $jadwal['lokasi'] ?? '-',
                'no_seri'   => $series['nomor_aset'] ?? $series['no_seri'] ?? '-',
                'kategori'  => $lkp['kategori'] ?? '-',
                'tanggal'   => $lkp['tanggal_pemeriksaan'] ?? '-',
                'teknisi'   => $lkp['teknisi'] ?? '-',
                'hasil'     => $lkp['hasil_pemeriksaan'] ?? '-',
                'catatan'   => $lkp['catatan'] ?? '-',
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
        
        $seriesModel = new \App\Models\AsetSeriesModel();
        $asetModel = new \App\Models\AsetModel();
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
        $headers = ['No', 'Nama Unit / Aset', 'Lokasi', 'No. Seri', 'Kategori', 'Tanggal', 'Teknisi', 'Hasil', 'Catatan / Temuan'];
        foreach (range('A', 'I') as $i => $col) {
            $sheet->setCellValue($col . '6', $headers[$i]);
            $sheet->mergeCells($col . '6:' . $col . '7');
        }
        
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A6:I7')->applyFromArray($headerStyle);
        
        // Data
        $row = 8;
        $no = 1;
        foreach ($filtered as $lkp) {
            $series = !empty($lkp['id_aset_series']) ? $seriesModel->getById($lkp['id_aset_series']) : null;
            $aset = $series ? $asetModel->getById($series['id_aset']) : null;
            $jadwal = !empty($lkp['id_jadwal']) ? $jadwalModel->getById($lkp['id_jadwal']) : null;
            
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $aset['nama'] ?? $jadwal['aset'] ?? '-');
            $sheet->setCellValue('C' . $row, $series['ruangan'] ?? $jadwal['lokasi'] ?? '-');
            $sheet->setCellValue('D' . $row, $series['nomor_aset'] ?? $series['no_seri'] ?? '-');
            $sheet->setCellValue('E' . $row, $lkp['kategori'] ?? '-');
            $sheet->setCellValue('F' . $row, $lkp['tanggal_pemeriksaan'] ?? '-');
            $sheet->setCellValue('G' . $row, $lkp['teknisi'] ?? '-');
            $sheet->setCellValue('H' . $row, $lkp['hasil_pemeriksaan'] ?? '-');
            $sheet->setCellValue('I' . $row, $lkp['catatan'] ?? '-');
            $row++;
        }
        
        if ($row > 8) {
            $sheet->getStyle('A8:I' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                'alignment' => ['vertical' => 'top', 'wrapText' => true]
            ]);
        }
        
        $cols = ['A'=>5, 'B'=>20, 'C'=>15, 'D'=>15, 'E'=>15, 'F'=>15, 'G'=>15, 'H'=>20, 'I'=>30];
        foreach ($cols as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        // Signature Block
        $sigRow = $row + 2;
        $sheet->setCellValue('I' . $sigRow, 'Yogyakarta, ' . date('d F Y'));
        $sheet->setCellValue('I' . ($sigRow + 1), 'Kepala IPSRS RSUD Kota YK,');
        
        $sheet->setCellValue('I' . ($sigRow + 5), \App\Config\IPSRS::NAMA_KEPALA);
        $sheet->getStyle('I' . ($sigRow + 5))->getFont()->setUnderline(true)->setBold(true);
        $sheet->setCellValue('I' . ($sigRow + 6), 'NIP. ' . \App\Config\IPSRS::NIP_KEPALA);
        
        $sheet->getStyle('I'.$sigRow.':I'.($sigRow+6))->getAlignment()->setHorizontal('center');
        
        $filename = 'Laporan_Preventif_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}

