<?php
$filterParam = $filter ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-800">Riwayat Transaksi</h1>
    <p class="text-sm text-gray-400 mt-0.5">Histori keluar masuk stok suku cadang</p>
  </div>
  <a href="/ipsrs/stok" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Kembali ke Stok
  </a>
</div>

<!-- Filter Tabs -->
<div class="flex flex-wrap gap-2 mb-4">
  <?php
  $tabs = ['' => 'Semua', 'Masuk' => 'Masuk', 'Keluar' => 'Keluar'];
  foreach ($tabs as $val => $label):
    $active = $filterParam === $val;
  ?>
  <a href="/ipsrs/stok/riwayat<?= $val ? '?jenis='.urlencode($val) : '' ?>"
     class="px-4 py-1.5 rounded-xl text-sm font-medium transition-colors
       <?= $active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' ?>">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Riwayat Table -->
<div class="card overflow-hidden">
  <?php if (empty($riwayat)): ?>
  <div class="text-center py-16">
    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm text-gray-400">Belum ada riwayat transaksi.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Barang</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Jenis</th>
          <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Jumlah</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">No. Dokumen</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Petugas</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($riwayat as $r): ?>
        <?php
          $jenis = strtolower($r['jenis'] ?? '');
          $jBadge = match($jenis) {
            'masuk'  => 'badge bg-emerald-100 text-emerald-700',
            'keluar' => 'badge bg-red-100 text-red-600',
            default  => 'badge bg-gray-100 text-gray-500',
          };
          $jLabel = ucfirst($jenis ?: '-');
          $qtyClass = $jenis === 'keluar' ? 'text-red-600 font-semibold' : 'text-emerald-600 font-semibold';
        ?>
        <tr class="hover:bg-gray-50/60 transition-colors">
          <td class="px-5 py-3.5 text-gray-600"><?= tgl($r['tanggal']) ?></td>
          <td class="px-5 py-3.5 font-medium text-gray-800"><?= esc($r['nama_barang'] ?? $r['nama'] ?? '-') ?></td>
          <td class="px-5 py-3.5"><span class="<?= $jBadge ?>"><?= $jLabel ?></span></td>
          <td class="px-5 py-3.5 text-right <?= $qtyClass ?>">
            <?= $jenis === 'keluar' ? '-' : '+' ?><?= (int)($r['jumlah'] ?? 0) ?>
          </td>
          <td class="px-5 py-3.5 text-gray-500 font-mono text-xs"><?= esc($r['no_dokumen'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($r['petugas'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-500 max-w-[180px] truncate"><?= esc($r['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
