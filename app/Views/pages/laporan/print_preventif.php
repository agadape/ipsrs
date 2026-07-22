<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Preventif IPSRS — <?= esc($periodLabel ?? 'Laporan') ?></title>
  <style>
    body {
      font-family: "Times New Roman", Times, serif;
      background: #fff;
      color: #000;
      margin: 0;
      padding: 40px;
      font-size: 11pt; /* slightly smaller to fit more columns */
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
    
    /* Data Table */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
      font-size: 10pt; /* smaller to fit 8 columns */
    }
    .data-table th, .data-table td {
      border: 1px solid #000;
      padding: 6px 4px;
      text-align: left;
      vertical-align: top;
    }
    .data-table th { text-align: center; font-weight: bold; background: #f9f9f9; }
    .text-center { text-align: center; }
    
    /* Signature */
    .signature { float: right; width: 300px; text-align: center; margin-top: 20px; font-size: 11pt; }
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
  <div class="report-title">LEMBAR KERJA PEMELIHARAAN PREVENTIF (LKP)</div>
  <div class="report-subtitle">Periode: <?= esc($periodLabel ?? '-') ?></div>

  <!-- Data Table -->
  <table class="data-table">
    <thead>
      <tr>
        <th width="3%">No</th>
        <th width="15%">Nama Unit / Aset</th>
        <th width="12%">Lokasi</th>
        <th width="10%">No. Seri</th>
        <th width="9%">Tanggal</th>
        <th width="11%">Hasil Pemeriksaan</th>
        <th width="17%">Temuan / Deskripsi</th>
        <th width="13%">Rencana Perbaikan</th>
        <th width="10%">Keterangan NL</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; foreach (($dataLKP ?? []) as $lkp): ?>
        <tr>
          <td class="text-center"><?= $no++ ?></td>
          <td><?= esc($lkp['nama_unit'] ?? '-') ?></td>
          <td><?= esc($lkp['lokasi'] ?? '-') ?></td>
          <td class="text-center"><?= esc($lkp['no_seri'] ?? '-') ?></td>
          <td class="text-center"><?= !empty($lkp['tanggal']) && $lkp['tanggal'] !== '-' ? date('d/m/Y', strtotime($lkp['tanggal'])) : '-' ?></td>
          <td class="text-center"><?= esc($lkp['hasil'] ?? '-') ?></td>
          <td><?= esc($lkp['temuan'] ?? '-') ?></td>
          <td><?= esc($lkp['rencana_perbaikan'] ?? '-') ?></td>
          <td class="text-center"><?= esc($lkp['keterangan'] ?? '-') ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($dataLKP)): ?>
        <tr>
          <td colspan="9" class="text-center" style="padding: 15px;">Tidak ada data laporan preventif untuk periode ini.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Signature Block -->
    <div class="signature">
      <p>Yogyakarta, <?= date('d F Y') ?></p>
      <p>Kepala IPSRS RSUD Kota YK,</p>
      <br><br><br><br>
      <p class="name"><?= \App\Config\IPSRS::NAMA_KEPALA ?></p>
      <p>NIP. <?= \App\Config\IPSRS::NIP_KEPALA ?></p>
    </div>
  
  <div class="clear"></div>

</body>
</html>
