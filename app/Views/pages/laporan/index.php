<?php
$period = $period ?? 'bulan';
?>

<!-- Page Header -->
<div class="flex flex-col gap-4 mb-6">
  <!-- Top row: Title & Tabs -->
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan</h1>
      <p class="text-sm text-slate-500 mt-1">Ringkasan kinerja sistem IPSRS</p>
    </div>
    <!-- Period Filter -->
    <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-md p-1">
    <?php foreach (['minggu' => 'Minggu', 'bulan' => 'Bulan', 'tahun' => 'Tahun'] as $val => $label): ?>
    <a href="/ipsrs/laporan?period=<?= $val ?>"
       class="px-4 py-1.5 rounded-sm text-xs font-medium transition-colors
         <?= $period === $val ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100' ?>">
      <?= $label ?>
    </a>
    <?php endforeach; ?>
    </div>
  </div>

  <!-- Bottom row: Export actions -->
  <div class="flex flex-wrap items-center justify-end gap-3">
    
    <!-- Group LK -->
    <div class="flex items-center bg-white border border-slate-200 rounded-md p-1 shadow-sm">
      <span class="text-[10px] font-semibold text-slate-500 px-3 uppercase tracking-wider border-r border-slate-200">Lap. Kerusakan</span>
      <a href="/ipsrs/laporan/export-print?period=<?= urlencode($period) ?>" target="_blank"
         class="flex items-center gap-1.5 px-3 py-1.5 hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-sm transition-colors">
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak PDF
      </a>
      <a href="/ipsrs/laporan/export-excel?period=<?= urlencode($period) ?>"
         class="flex items-center gap-1.5 px-3 py-1.5 hover:bg-emerald-50 text-emerald-700 text-xs font-medium rounded-sm transition-colors">
        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Excel
      </a>
    </div>

    <!-- Group Preventif -->
    <div class="flex items-center bg-white border border-slate-200 rounded-md p-1 shadow-sm">
      <span class="text-[10px] font-semibold text-slate-500 px-3 uppercase tracking-wider border-r border-slate-200">Preventif</span>
      <a href="/ipsrs/laporan/export-print-preventif?period=<?= urlencode($period) ?>" target="_blank"
         class="flex items-center gap-1.5 px-3 py-1.5 hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-sm transition-colors">
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak PDF
      </a>
      <a href="/ipsrs/laporan/export-excel-preventif?period=<?= urlencode($period) ?>"
         class="flex items-center gap-1.5 px-3 py-1.5 hover:bg-emerald-50 text-emerald-700 text-xs font-medium rounded-sm transition-colors">
        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Excel
      </a>
    </div>

  </div><!-- /actions -->
</div><!-- /header -->

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
  <?php
  $stats = [
    ['label' => 'Total LK',    'value' => $totalLK ?? 0,         'unit' => '',      'color' => 'text-slate-900'],
    ['label' => 'Selesai',     'value' => $selesai ?? 0,          'unit' => '',      'color' => 'text-emerald-600'],
    ['label' => 'Aktif',       'value' => $aktif ?? 0,            'unit' => '',      'color' => 'text-red-700'],
    ['label' => 'SLA',         'value' => number_format($slaPct ?? 0, 1), 'unit' => '%', 'color' => 'text-slate-900'],
    ['label' => 'Avg. Respon', 'value' => number_format($avgRespon ?? 0, 0), 'unit' => ' mnt', 'color' => 'text-slate-900'],
    ['label' => 'PM Selesai',  'value' => ($jadwalSelesai ?? 0).'/'
                                        .($jadwalTotal ?? 0),     'unit' => '',      'color' => 'text-slate-900'],
  ];
  foreach ($stats as $st):
  ?>
  <div class="card p-4">
    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1"><?= $st['label'] ?></p>
    <p class="text-2xl font-bold <?= $st['color'] ?> tracking-tight"><?= $st['value'] ?><span class="text-sm font-medium text-slate-400"><?= $st['unit'] ?></span></p>
  </div>
  <?php endforeach; ?>
</div>

<!-- LK by Kode + Stok Summary -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

  <!-- LK by Kode -->
  <div class="card p-6 lg:col-span-2">
    <h2 class="text-sm font-semibold text-slate-800 mb-4">LK Berdasarkan Kode</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <?php
      $kodeColors = [
        'AC' => 'bg-red-50 text-red-800 border-red-200', 
        'PR' => 'bg-slate-50 text-slate-700 border-slate-200', 
        'NM' => 'bg-slate-50 text-slate-700 border-slate-200', 
        'AL' => 'bg-slate-50 text-slate-500 border-slate-200'
      ];
      $kodeLabels = ['AC' => 'Air Conditioning', 'PR' => 'Prasarana', 'NM' => 'Non Medis', 'AL' => 'Alat Lainnya'];
      foreach (['AC', 'PR', 'NM', 'AL'] as $kode):
        $count = ($kodeGroups ?? [])[$kode] ?? 0;
        $cls   = $kodeColors[$kode] ?? 'bg-slate-50 text-slate-700 border-slate-200';
      ?>
      <div class="border rounded-md p-4 text-center <?= $cls ?>">
        <p class="text-2xl font-bold tracking-tight"><?= (int)$count ?></p>
        <p class="text-xs font-bold mt-1"><?= $kode ?></p>
        <p class="text-[10px] opacity-75 mt-0.5"><?= $kodeLabels[$kode] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Stok Summary -->
  <div class="card p-6">
    <h2 class="text-sm font-semibold text-slate-800 mb-4">Ringkasan Stok</h2>
    <div class="space-y-4">
      <div class="flex items-center justify-between px-4 py-3 bg-red-50 border border-red-200 rounded-md">
        <div>
          <p class="text-xs font-semibold text-red-700">Stok Habis</p>
          <p class="text-[10px] text-red-500 mt-0.5">Perlu restock segera</p>
        </div>
        <span class="text-2xl font-bold text-red-700 tracking-tight"><?= (int)($stokHabis ?? 0) ?></span>
      </div>
      <div class="flex items-center justify-between px-4 py-3 bg-amber-50 border border-amber-200 rounded-md">
        <div>
          <p class="text-xs font-semibold text-amber-700">Stok Menipis</p>
          <p class="text-[10px] text-amber-500 mt-0.5">Di bawah minimum</p>
        </div>
        <span class="text-2xl font-bold text-amber-700 tracking-tight"><?= (int)($stokMenipis ?? 0) ?></span>
      </div>
      <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border border-slate-200 rounded-md">
        <div>
          <p class="text-xs font-semibold text-slate-700">Total Item</p>
          <p class="text-[10px] text-slate-500 mt-0.5">Semua material</p>
        </div>
        <span class="text-2xl font-bold text-slate-700 tracking-tight"><?= count($allStok ?? []) ?></span>
      </div>
    </div>
  </div>

</div>

<!-- Filtered LK Table -->
<div class="card p-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-sm font-semibold text-slate-800">Daftar Laporan Kerusakan</h2>
    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($filteredLK ?? []) ?> laporan</span>
  </div>

  <?php if (empty($filteredLK)): ?>
  <p class="text-sm text-slate-500 text-center py-8">Tidak ada laporan untuk periode ini.</p>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left text-sm border-collapse">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">No. Order</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Keluhan</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Respon</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($filteredLK as $lk): ?>
        <?php
          $s = $lk['status'] ?? '';
          $sBadge = status_lk_badge($s);
          $rt = (int)($lk['response_time'] ?? 0);
        ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 font-mono text-xs text-red-700 font-semibold"><?= esc($lk['no_order'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= tgl($lk['tanggal']) ?></td>
          <td class="px-4 py-3 text-slate-800 max-w-[220px] truncate"><?= esc($lk['keluhan'] ?? '-') ?></td>
          <td class="px-4 py-3"><span class="<?= $sBadge ?>"><?= esc($s) ?></span></td>
          <td class="px-4 py-3 <?= $rt > 15 ? 'text-red-600 font-semibold' : 'text-slate-600' ?>">
            <?= $rt > 0 ? $rt.' mnt' : '-' ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
