<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan IPSRS — <?= esc($periodLabel ?? 'Laporan') ?></title>
  <style>
    body {
      font-family: "Times New Roman", Times, serif;
      background: #fff;
      color: #000;
      margin: 0;
      padding: 40px;
      font-size: 12pt;
    }
    .no-print { margin-bottom: 20px; text-align: right; }
    .btn-print { 
      padding: 8px 16px; 
      font-family: Arial, sans-serif; 
      cursor: pointer; 
      background: #eee; 
      border: 1px solid #ccc; 
      border-radius: 4px;
    }
    
    /* Kop Surat */
    .kop-surat {
      text-align: center;
      border-bottom: 3px solid #000;
      padding-bottom: 10px;
      margin-bottom: 2px;
    }
    .kop-surat h1 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
    .kop-surat h2 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; }
    .kop-surat p { margin: 2px 0; font-size: 11pt; }
    .kop-line-2 { border-top: 1px solid #000; margin-bottom: 20px; }
    
    /* Content */
    .report-title {
      text-align: center;
      font-size: 14pt;
      font-weight: bold;
      text-decoration: underline;
      margin-bottom: 5px;
    }
    .report-subtitle { text-align: center; margin-bottom: 25px; font-size: 12pt; }
    
    /* Summary */
    .summary-table { width: 100%; margin-bottom: 25px; font-size: 11pt; border-collapse: collapse; }
    .summary-table td { padding: 4px; }
    
    /* Data Table */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
      font-size: 11pt;
    }
    .data-table th, .data-table td {
      border: 1px solid #000;
      padding: 6px 8px;
      text-align: left;
    }
    .data-table th { text-align: center; font-weight: bold; }
    .text-center { text-align: center; }
    
    /* Signature */
    .signature { float: right; width: 300px; text-align: center; margin-top: 20px; }
    .signature p { margin: 0; }
    .signature .name { margin-top: 70px; font-weight: bold; text-decoration: underline; }
    
    .clear { clear: both; }

    @media print {
      body { padding: 0; }
      .no-print { display: none !important; }
      table { page-break-inside: auto; }
      tr { page-break-inside: avoid; }
      @page { size: A4 landscape; margin: 1.5cm; }
    }
  </style>
</head>
<body>

  <!-- Print Button -->
  <div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Dokumen</button>
  </div>

  <!-- Kop Surat -->
  <div class="kop-surat">
    <h1>PEMERINTAH KOTA YOGYAKARTA</h1>
    <h2>RUMAH SAKIT UMUM DAERAH</h2>
    <p>Jl. Ki Ageng Pemanahan No.1, Sorosutan, Kec. Umbulharjo, Kota Yogyakarta, DIY 55162</p>
    <p>Telepon: (0274) 371195 | Email: rsud@jogjakota.go.id</p>
  </div>
  <div class="kop-line-2"></div>

  <!-- Title -->
  <div class="report-title">LAPORAN KINERJA PEMELIHARAAN SARANA & PRASARANA</div>
  <div class="report-subtitle">Periode: <?= esc($periodLabel ?? '-') ?></div>

  <!-- Summary -->
  <table class="summary-table">
    <tr>
      <td width="20%"><strong>Total Laporan</strong></td>
      <td width="2%">:</td>
      <td width="28%"><?= esc($totalLK ?? 0) ?> laporan</td>
      <td width="20%"><strong>Rata-rata Respon</strong></td>
      <td width="2%">:</td>
      <td width="28%"><?= esc($avgRespon ?? 0) ?> menit</td>
    </tr>
    <tr>
      <td><strong>Selesai</strong></td>
      <td>:</td>
      <td><?= esc($selesai ?? 0) ?> laporan</td>
      <td><strong>Tindakan Preventif</strong></td>
      <td>:</td>
      <td><?= esc($jadwalSelesai ?? 0) ?> dari <?= esc($jadwalTotal ?? 0) ?> jadwal selesai</td>
    </tr>
    <tr>
      <td><strong>Aktif/Proses</strong></td>
      <td>:</td>
      <td><?= esc($aktif ?? 0) ?> laporan</td>
      <td><strong>SLA Tercapai</strong></td>
      <td>:</td>
      <td><?= esc($slaPct ?? 0) ?>%</td>
    </tr>
  </table>

  <table class="data-table">
    <thead>
      <tr>
        <th width="4%">No</th>
        <th width="11%">Tanggal</th>
        <th width="14%">No. Order</th>
        <th width="14%">Nama Alat/Aset</th>
        <th width="12%">Lokasi</th>
        <th width="20%">Keluhan & Tindakan</th>
        <th width="15%">Suku Cadang</th>
        <th width="10%">Teknisi</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; foreach (($filteredLK ?? []) as $lk): ?>
        <tr>
          <td class="text-center"><?= $no++ ?></td>
          <td class="text-center"><?= date('d/m/Y', strtotime($lk['tanggal'])) ?></td>
          <td class="text-center"><?= esc($lk['no_order'] ?? '-') ?></td>
          <td><?= esc($lk['nama_aset'] ?? '-') ?></td>
          <td class="text-center"><?= esc($lk['lokasi'] ?? '-') ?></td>
          <td>
            <strong>Keluhan:</strong> <?= esc($lk['keluhan'] ?? '-') ?><br>
            <strong>Tindakan:</strong> <?= esc($lk['tindakan'] ?? '-') ?>
          </td>
          <td><?= esc($lk['suku_cadang_str'] ?? '-') ?></td>
          <td><?= esc($lk['teknisi'] ?? '-') ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($filteredLK)): ?>
        <tr>
          <td colspan="8" class="text-center">Tidak ada data untuk periode ini.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Signature Block -->
  <div class="signature">
    <p>Yogyakarta, <?= date('d F Y') ?></p>
    <p>Kepala IPSRS RSUD Kota YK,</p>
    <br><br><br><br>
    <p class="name">(........................................................)</p>
    <p>NIP. ........................................</p>
  </div>
  
  <div class="clear"></div>

</body>
</html>