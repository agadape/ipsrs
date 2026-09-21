<?php

namespace App\Controllers;

use App\Config\IPSRS;
use App\Libraries\ReportPeriod;
use App\Libraries\SpreadsheetText;
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

        $periodInfo = ReportPeriod::describe($period);
        $period = $periodInfo['key'];
        $filteredLK = ReportPeriod::filterRows($allLK, 'tanggal', $periodInfo);

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

        $jadwalPeriode = ReportPeriod::filterRows($allJadwal, 'tanggal', $periodInfo);
        $jadwalSelesai = count(array_filter($jadwalPeriode, fn($j) => $j['status'] === IPSRS::STATUS_JADWAL[1]));
        $jadwalTotal   = count($jadwalPeriode);
        $pmPct         = $jadwalTotal > 0 ? round($jadwalSelesai / $jadwalTotal * 100) : 0;

        $kodeGroups = [];
        foreach ($filteredLK as $l) {
            $kodeGroups[$l['kode']] = ($kodeGroups[$l['kode']] ?? 0) + 1;
        }

        return compact(
            'period', 'periodInfo', 'filteredLK', 'allStok', 'allJadwal',
            'totalLK', 'selesai', 'aktif', 'slaPct', 'avgRespon',
            'stokHabis', 'stokMenipis', 'jadwalSelesai', 'jadwalTotal', 'pmPct',
            'kodeGroups'
        );
    }

    /** @return array{period: string, periodInfo: array<string, string>, filtered: array<int, array<string, mixed>>, dataLKP: array<int, array<string, mixed>>} */
    private function getPreventiveData(string $period): array
    {
        $periodInfo = ReportPeriod::describe($period);
        $filtered = ReportPeriod::filterRows((new \App\Models\LkpModel())->getAll(), 'tanggal_pemeriksaan', $periodInfo);

        $seriesModel = new \App\Models\AsetSeriesModel();
        $asetModel = new \App\Models\AsetModel();
        $jadwalModel = new \App\Models\JadwalModel();
        $dataLKP = [];

        foreach ($filtered as $lkp) {
            $jadwal = !empty($lkp['id_jadwal']) ? $jadwalModel->getById($lkp['id_jadwal']) : null;
            $series = !empty($lkp['id_aset_series']) ? $seriesModel->getById($lkp['id_aset_series']) : null;
            $aset = $series ? $asetModel->getById($series['id_aset']) : null;

            $dataLKP[] = [
                // Schedule text is the closest available event snapshot. Current
                // series/master values are only a fallback for legacy records.
                'nama_unit' => $jadwal['aset'] ?? $aset['nama'] ?? '-',
                'lokasi' => $jadwal['lokasi'] ?? $series['ruangan'] ?? '-',
                'nomor_inventaris' => $series['nomor_aset'] ?? '-',
                'kategori' => $lkp['kategori'] ?? '-',
                'tanggal' => $lkp['tanggal_pemeriksaan'] ?? '-',
                'teknisi' => $lkp['teknisi'] ?? '-',
                'hasil' => $lkp['hasil_pemeriksaan'] ?? '-',
                'catatan' => $lkp['catatan'] ?? '-',
            ];
        }

        return [
            'period' => $periodInfo['key'],
            'periodInfo' => $periodInfo,
            'filtered' => $filtered,
            'dataLKP' => $dataLKP,
        ];
    }

    public function index(): string
    {
        $period = $this->request->getGet('period') ?? 'bulan';
        $data   = $this->getData($period);
        return $this->render('pages/laporan/index', $data);
    }

    public function exportExcelLK()
    {
        $period     = $this->request->getGet('period') ?? 'bulan';
        $data       = $this->getData($period);
        $period = $data['period'];
        $filteredLK = $data['filteredLK'];
        $periodStr = $data['periodInfo']['label'];

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
            SpreadsheetText::set($sheet, 'B' . $row, $l['no_order'] ?? '-');
            SpreadsheetText::set($sheet, 'C' . $row, $l['tanggal'] ?? '-');
            SpreadsheetText::set($sheet, 'D' . $row, $l['jam_laporan'] ?? '-');
            SpreadsheetText::set($sheet, 'E' . $row, ($l['pelapor'] ?? '-') . ' (' . ($l['unit_pelapor'] ?? '-') . ')');
            SpreadsheetText::set($sheet, 'F' . $row, $asetStr);
            SpreadsheetText::set($sheet, 'G' . $row, $l['lokasi'] ?? $series['ruangan'] ?? '-');
            SpreadsheetText::set($sheet, 'H' . $row, $l['keluhan'] ?? '-');
            SpreadsheetText::set($sheet, 'I' . $row, $l['status'] ?? '-');
            SpreadsheetText::set($sheet, 'J' . $row, $l['teknisi'] ?? '-');
            SpreadsheetText::set($sheet, 'K' . $row, $l['tindakan'] ?? '-');
            SpreadsheetText::set($sheet, 'L' . $row, $l['response_time'] ?? '-');
            SpreadsheetText::set($sheet, 'M' . $row, $l['down_time'] ?? '-');
            
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
        $data['periodLabel'] = $data['periodInfo']['label'];
        
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
        $data = $this->getPreventiveData($period);

        return view('pages/laporan/print_preventif', [
            'periodLabel' => $data['periodInfo']['label'],
            'dataLKP' => $data['dataLKP'],
        ]);
    }

    public function exportExcelPreventif()
    {
        $period = $this->request->getGet('period') ?? 'bulan';
        $data = $this->getPreventiveData($period);
        $periodInfo = $data['periodInfo'];
        
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
        
        $sheet->setCellValue('A3', 'Periode: ' . $periodInfo['label']);
        $sheet->mergeCells('A3:K3');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
        
        // Metadata (Rows 4-5)
        $sheet->setCellValue('A4', 'Dasar Periode');
        $sheet->setCellValue('C4', 'Tanggal pemeriksaan LKP');
        $sheet->setCellValue('A5', 'Rentang Tanggal');
        $sheet->setCellValue('C5', $periodInfo['start'] . ' s.d. ' . $periodInfo['end']);
        
        // Headers (Rows 6-7)
        $headers = ['No', 'Nama Unit / Aset', 'Lokasi', 'No. Inventaris', 'Kategori', 'Tanggal', 'Teknisi', 'Hasil', 'Catatan / Temuan'];
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
        foreach ($data['dataLKP'] as $lkp) {
            $sheet->setCellValue('A' . $row, $no++);
            SpreadsheetText::set($sheet, 'B' . $row, $lkp['nama_unit']);
            SpreadsheetText::set($sheet, 'C' . $row, $lkp['lokasi']);
            SpreadsheetText::set($sheet, 'D' . $row, $lkp['nomor_inventaris']);
            SpreadsheetText::set($sheet, 'E' . $row, $lkp['kategori']);
            SpreadsheetText::set($sheet, 'F' . $row, $lkp['tanggal']);
            SpreadsheetText::set($sheet, 'G' . $row, $lkp['teknisi'] ?? '-');
            SpreadsheetText::set($sheet, 'H' . $row, $lkp['hasil_pemeriksaan'] ?? '-');
            SpreadsheetText::set($sheet, 'I' . $row, $lkp['catatan'] ?? '-');
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

