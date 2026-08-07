<?php
$filterParam = $filter ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Transaksi</h1>
    <p class="text-sm font-medium text-slate-500 mt-1">Histori keluar masuk stok suku cadang</p>
  </div>
  <a href="/ipsrs/stok" class="text-sm font-medium text-red-700 hover:text-red-900 hover:underline flex items-center gap-1 transition-colors">
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
     class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors border
       <?= $active ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900' ?>">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Riwayat Table -->
<div class="card overflow-hidden">
  <?php if (empty($riwayat)): ?>
  <div class="text-center py-16">
    <svg class="w-12 h-12 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm text-slate-500 font-medium">Belum ada riwayat transaksi.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Barang</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Jenis</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider text-right">Jumlah</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">No. Dokumen</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Petugas</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($riwayat as $r): ?>
        <?php
          $jenis = strtolower($r['jenis'] ?? '');
          $jBadge = match($jenis) {
            'masuk'  => 'px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200',
            'keluar' => 'px-2 py-0.5 rounded text-[11px] font-medium bg-red-50 text-red-700 border border-red-200',
            default  => 'px-2 py-0.5 rounded text-[11px] font-medium bg-slate-50 text-slate-700 border border-slate-200',
          };
          $jLabel = ucfirst($jenis ?: '-');
          $qtyClass = $jenis === 'keluar' ? 'text-red-600 font-semibold' : 'text-emerald-600 font-semibold';
        ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 text-slate-600"><?= tgl($r['tanggal']) ?></td>
          <td class="px-4 py-3 font-medium text-slate-900 group-hover:text-red-700 transition-colors"><?= esc($r['nama_barang'] ?? $r['nama'] ?? '-') ?></td>
          <td class="px-4 py-3"><span class="<?= $jBadge ?> shadow-sm"><?= $jLabel ?></span></td>
          <td class="px-4 py-3 text-right <?= $qtyClass ?>">
            <?= $jenis === 'keluar' ? '-' : '+' ?><?= (int)($r['jumlah'] ?? 0) ?>
          </td>
          <td class="px-4 py-3 text-slate-500 font-mono text-xs"><?= esc($r['no_dokumen'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($r['petugas'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-500 max-w-[180px] truncate"><?= esc($r['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
