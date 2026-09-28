<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Unit Aset — RSUD Kota Yogyakarta</title>
  <style>
    body { font-family: Arial, sans-serif; color: #111827; margin: 24px; font-size: 10pt; }
    h1 { font-size: 16pt; margin: 0 0 4px; text-align: center; }
    .sub { text-align: center; margin-bottom: 20px; }
    .actions { text-align: right; margin-bottom: 16px; }
    .actions button { padding: 8px 14px; cursor: pointer; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    th, td { border: 1px solid #333; padding: 5px; text-align: left; vertical-align: top; overflow-wrap: anywhere; }
    th { background: #e5e7eb; }
    thead { display: table-header-group; }
    tr { break-inside: avoid; }
    @media print { .actions { display: none; } body { margin: 0; } @page { size: A4 landscape; margin: 1cm; } }
  </style>
</head>
<body>
  <div class="actions"><button type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button></div>
  <h1>DAFTAR UNIT ASET</h1>
  <div class="sub">RSUD Kota Yogyakarta · Posisi saat ekspor: <?= esc($exportedAt) ?></div>
  <table>
    <thead><tr>
      <th style="width:4%">No</th><th style="width:12%">No. Inventaris</th><th style="width:16%">Nama Aset</th>
      <th style="width:10%">Kategori</th><th style="width:10%">Merk / Model</th><th style="width:11%">No. Seri</th>
      <th style="width:25%">Lokasi</th><th style="width:12%">Status</th>
    </tr></thead>
    <tbody>
      <?php foreach ($aset as $index => $unit): ?>
      <tr>
        <td><?= $index + 1 ?></td>
        <td><?= esc($unit['nomor_aset'] ?? '-') ?></td>
        <td><?= esc($unit['nama'] ?? '-') ?></td>
        <td><?= esc($unit['kategori'] ?? '-') ?></td>
        <td><?= esc(trim(($unit['merk'] ?? '') . ' ' . ($unit['model'] ?? '')) ?: '-') ?></td>
        <td><?= esc($unit['no_seri'] ?? '-') ?></td>
        <td><?= esc(implode(' / ', array_filter([$unit['gedung'] ?? null, $unit['lantai'] ?? null, $unit['ruangan'] ?? null, $unit['unit'] ?? null])) ?: '-') ?></td>
        <td><?= esc($unit['status'] ?? '-') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($aset)): ?><tr><td colspan="8" style="text-align:center">Belum ada unit aset.</td></tr><?php endif; ?>
    </tbody>
  </table>
</body>
</html>
