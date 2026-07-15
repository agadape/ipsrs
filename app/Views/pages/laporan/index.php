<?php
$period = $period ?? 'bulan';
?>

<!-- Page Header -->
<div class="flex flex-col gap-4 mb-6">
  <!-- Top row: Title & Tabs -->
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-800">Laporan</h1>
      <p class="text-sm text-gray-400 mt-0.5">Ringkasan kinerja sistem IPSRS</p>
    </div>
    <!-- Period Filter -->
    <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
    <?php foreach (['minggu' => 'Minggu', 'bulan' => 'Bulan', 'tahun' => 'Tahun'] as $val => $label): ?>
    <a href="/ipsrs/laporan?period=<?= $val ?>"
       class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all duration-300
         <?= $period === $val ? 'bg-teal-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' ?>">
      <?= $label ?>
    </a>
    <?php endforeach; ?>
    </div>
  </div>

  <!-- Bottom row: Export actions -->
  <div class="flex flex-wrap items-center justify-end gap-3">
    
    <!-- Group LK -->
    <div class="flex items-center bg-white border border-indigo-100 rounded-xl p-1 shadow-sm hover:border-indigo-300 transition-colors">
      <span class="text-[10px] font-bold text-indigo-400 px-3 uppercase tracking-wider border-r border-gray-100">Lap. Kerusakan</span>
      <a href="/ipsrs/laporan/export-print?period=<?= urlencode($period) ?>" target="_blank"
         class="flex items-center gap-1.5 px-3 py-1.5 hover:bg-teal-50 text-teal-600 text-xs font-bold rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak PDF
      </a>
      <a href="/ipsrs/laporan/export-csv?period=<?= urlencode($period) ?>"
         class="flex items-center gap-1.5 px-3 py-1.5 hover:bg-emerald-50 text-emerald-600 text-xs font-bold rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        CSV
      </a>
    </div>

    <!-- Group Preventif -->
    <div class="flex items-center bg-white border border-orange-100 rounded-xl p-1 shadow-sm hover:border-orange-300 transition-colors">
      <span class="text-[10px] font-bold text-orange-400 px-3 uppercase tracking-wider border-r border-gray-100">Preventif</span>
      <a href="/ipsrs/laporan/export-print-preventif?period=<?= urlencode($period) ?>" target="_blank"
         class="flex items-center gap-1.5 px-3 py-1.5 hover:bg-orange-50 text-orange-600 text-xs font-bold rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak PDF
      </a>
      <a href="/ipsrs/laporan/export-excel-preventif?period=<?= urlencode($period) ?>"
         class="flex items-center gap-1.5 px-3 py-1.5 hover:bg-teal-50 text-teal-600 text-xs font-bold rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Excel
      </a>
    </div>

  </div><!-- /actions -->
</div><!-- /header -->

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
  <?php
  $stats = [
    ['label' => 'Total LK',    'value' => $totalLK ?? 0,         'unit' => '',      'color' => 'text-gray-800'],
    ['label' => 'Selesai',     'value' => $selesai ?? 0,          'unit' => '',      'color' => 'text-emerald-600'],
    ['label' => 'Aktif',       'value' => $aktif ?? 0,            'unit' => '',      'color' => 'text-teal-600'],
    ['label' => 'SLA',         'value' => number_format($slaPct ?? 0, 1), 'unit' => '%', 'color' => 'text-gray-800'],
    ['label' => 'Avg. Respon', 'value' => number_format($avgRespon ?? 0, 0), 'unit' => ' mnt', 'color' => 'text-gray-800'],
    ['label' => 'PM Selesai',  'value' => ($jadwalSelesai ?? 0).'/'
                                        .($jadwalTotal ?? 0),     'unit' => '',      'color' => 'text-gray-800'],
  ];
  foreach ($stats as $st):
  ?>
  <div class="card px-4 py-4 text-center">
    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1"><?= $st['label'] ?></p>
    <p class="text-2xl font-bold <?= $st['color'] ?>"><?= $st['value'] ?><span class="text-sm font-medium text-gray-400"><?= $st['unit'] ?></span></p>
  </div>
  <?php endforeach; ?>
</div>

<!-- LK by Kode + Stok Summary -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

  <!-- LK by Kode -->
  <div class="card p-5 lg:col-span-2">
    <h2 class="text-sm font-semibold text-gray-700 mb-4">LK Berdasarkan Kode</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      <?php
      $kodeColors = ['AC' => 'bg-teal-50 text-teal-700 border-indigo-100', 'PR' => 'bg-slate-50 text-slate-700 border-slate-200', 'NM' => 'bg-gray-50 text-gray-700 border-gray-200', 'AL' => 'bg-gray-50 text-gray-500 border-gray-200'];
      $kodeLabels = ['AC' => 'Air Conditioning', 'PR' => 'Prasarana', 'NM' => 'Non Medis', 'AL' => 'Alat Lainnya'];
      foreach (['AC', 'PR', 'NM', 'AL'] as $kode):
        $count = ($kodeGroups ?? [])[$kode] ?? 0;
        $cls   = $kodeColors[$kode] ?? 'bg-gray-50 text-gray-700 border-gray-100';
      ?>
      <div class="border rounded-2xl p-4 text-center <?= $cls ?>">
        <p class="text-2xl font-bold"><?= (int)$count ?></p>
        <p class="text-xs font-bold mt-1"><?= $kode ?></p>
        <p class="text-[10px] opacity-70 mt-0.5"><?= $kodeLabels[$kode] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Stok Summary -->
  <div class="card p-5">
    <h2 class="text-sm font-semibold text-gray-700 mb-4">Ringkasan Stok</h2>
    <div class="space-y-3">
      <div class="flex items-center justify-between px-4 py-3 bg-red-50 rounded-xl">
        <div>
          <p class="text-xs font-medium text-red-600">Stok Habis</p>
          <p class="text-[10px] text-red-400 mt-0.5">Perlu restock segera</p>
        </div>
        <span class="text-2xl font-bold text-red-600"><?= (int)($stokHabis ?? 0) ?></span>
      </div>
      <div class="flex items-center justify-between px-4 py-3 bg-amber-50 rounded-xl">
        <div>
          <p class="text-xs font-medium text-amber-600">Stok Menipis</p>
          <p class="text-[10px] text-amber-400 mt-0.5">Di bawah minimum</p>
        </div>
        <span class="text-2xl font-bold text-amber-600"><?= (int)($stokMenipis ?? 0) ?></span>
      </div>
      <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-xl">
        <div>
          <p class="text-xs font-medium text-gray-600">Total Item</p>
          <p class="text-[10px] text-gray-400 mt-0.5">Semua material</p>
        </div>
        <span class="text-2xl font-bold text-gray-700"><?= count($allStok ?? []) ?></span>
      </div>
    </div>
  </div>

</div>

<!-- Filtered LK Table -->
<div class="card p-5">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-sm font-semibold text-gray-700">Daftar Laporan Kerusakan</h2>
    <span class="text-xs text-gray-400"><?= count($filteredLK ?? []) ?> laporan</span>
  </div>

  <?php if (empty($filteredLK)): ?>
  <p class="text-sm text-gray-400 text-center py-8">Tidak ada laporan untuk periode ini.</p>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50/80 border-b border-gray-200/60">
        <tr>
          <th class="text-left px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest rounded-l-xl">No. Order</th>
          <th class="text-left px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Tanggal</th>
          <th class="text-left px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Keluhan</th>
          <th class="text-left px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
          <th class="text-left px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest rounded-r-xl">Respon</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($filteredLK as $lk): ?>
        <?php
          $s = $lk['status'] ?? '';
          $sBadge = status_lk_badge($s);
          $rt = (int)($lk['response_time'] ?? 0);
        ?>
        <tr class="hover:bg-teal-50/40 transition-colors group">
          <td class="px-4 py-3 font-mono text-xs text-teal-600 font-semibold"><?= esc($lk['no_order'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-600"><?= tgl($lk['tanggal']) ?></td>
          <td class="px-4 py-3 text-gray-800 max-w-[220px] truncate"><?= esc($lk['keluhan'] ?? '-') ?></td>
          <td class="px-4 py-3"><span class="<?= $sBadge ?>"><?= esc($s) ?></span></td>
          <td class="px-4 py-3 <?= $rt > 15 ? 'text-red-600 font-semibold' : 'text-gray-600' ?>">
            <?= $rt > 0 ? $rt.' mnt' : '-' ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
