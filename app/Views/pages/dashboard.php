<?php
$today = $today ?? date('Y-m-d');
$hour = (int) date('H');
$sapa = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
$firstName = explode(' ', session('user_name') ?? 'Admin')[0];
?>

<!-- ════════════════════════════════════════════════════════════════════════
     HERO BANNER — Clean Enterprise Header
     ════════════════════════════════════════════════════════════════════════ -->
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight"><?= esc($sapa) ?>, <?= esc($firstName) ?></h1>
    <p class="text-sm text-slate-500 mt-1"><?= tgl($today, 'l, d F Y') ?> &middot; Ikhtisar sistem hari ini</p>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════
     KPI Cards — High Density
     ════════════════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
  <!-- SLA -->
  <div class="card p-4">
    <div class="flex items-center justify-between mb-2">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">SLA Respon</p>
      <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <p class="text-2xl font-bold text-slate-900"><?= number_format($slaPct ?? 0, 1) ?><span class="text-sm text-slate-500 font-medium ml-1">%</span></p>
    <div class="mt-2.5 h-1 rounded-full bg-slate-100">
      <div class="h-1 rounded-full bg-red-700" style="width:<?= min(100, (float) ($slaPct ?? 0)) ?>%"></div>
    </div>
    <p class="text-xs text-slate-500 mt-1.5">Target &lt;15 menit</p>
  </div>

  <!-- Rata-rata Respon -->
  <div class="card p-4">
    <div class="flex items-center justify-between mb-2">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rata-rata Respon</p>
      <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
    </div>
    <p class="text-2xl font-bold text-slate-900"><?= number_format($avgRespon ?? 0, 0) ?><span class="text-sm text-slate-500 font-medium ml-1">mnt</span></p>
    <p class="text-xs text-slate-500 mt-2.5">Waktu respons rata-rata</p>
  </div>

  <!-- PM Progress -->
  <div class="card p-4">
    <div class="flex items-center justify-between mb-2">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Preventif (PM)</p>
      <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <p class="text-2xl font-bold text-slate-900"><?= (int) ($jadwalSelesai ?? 0) ?><span class="text-sm text-slate-500 font-medium mx-1">/</span><span class="text-lg text-slate-700"><?= (int) ($jadwalTotal ?? 0) ?></span></p>
    <div class="mt-2.5 h-1 rounded-full bg-slate-100">
      <div class="h-1 rounded-full bg-emerald-500" style="width:<?= min(100, (float) ($pmPct ?? 0)) ?>%"></div>
    </div>
    <p class="text-xs text-slate-500 mt-1.5"><?= number_format($pmPct ?? 0, 0) ?>% selesai bulan ini</p>
  </div>

  <!-- LK Aktif -->
  <div class="card p-4">
    <div class="flex items-center justify-between mb-2">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">LK Aktif</p>
      <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
    </div>
    <p class="text-2xl font-bold text-slate-900"><?= (int) ($activeLK ?? 0) ?></p>
    <p class="text-xs text-slate-500 mt-2.5">Sedang dalam pengerjaan</p>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════════════════════
     PRIORITY + STATUS OVERVIEW — Side by Side
     ════════════════════════════════════════════════════════════════════════ -->
<!-- ════════════════════════════════════════════════════════════════════════
     PRIORITY + STATUS OVERVIEW — Side by Side
     ════════════════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

  <!-- Priority Alert Stack (1/3 width) -->
  <?php if (!empty($priority)): ?>
  <div class="card p-5">
    <div class="flex items-center gap-2 mb-4">
      <div class="w-2 h-2 rounded-full bg-red-500"></div>
      <h2 class="text-sm font-semibold text-slate-800">Perlu Ditindak</h2>
    </div>
      <div class="space-y-3">
        <?php foreach ($priority as $p): ?>
        <?php $accent = ($p['level'] === 'critical') ? 'border-red-200' : 'border-amber-200'; ?>
        <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100 last:border-0 last:pb-0">
          <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-slate-900 leading-snug truncate"><?= esc($p['title'] ?? '') ?></p>
            <p class="text-xs text-slate-500 mt-0.5 truncate"><?= esc($p['desc'] ?? '') ?></p>
          </div>
          <a href="<?= esc($p['path'] ?? '#') ?>"
             class="shrink-0 text-xs font-medium px-3 py-1.5 rounded border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors">
            <?= esc($p['action'] ?? 'Lihat') ?>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
  </div>
  <?php else: ?>
  <div class="card p-5 flex flex-col items-center justify-center text-center">
    <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center mb-3">
      <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
      </svg>
    </div>
    <p class="text-sm font-medium text-slate-700">Semua aman</p>
    <p class="text-xs text-slate-500 mt-0.5">Tidak ada item prioritas</p>
  </div>
  <?php endif; ?>

  <!-- Status Overview (2/3 width) -->
  <div class="lg:col-span-2 card p-5">
    <h2 class="text-sm font-semibold text-slate-800 mb-4">Status Operasional</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-4">
      
      <!-- Belum Disurvei -->
      <a href="/ipsrs/lk?status=Laporan+Masuk"
         class="group flex items-start flex-col gap-2 p-4 rounded-lg bg-slate-50 border border-slate-200 hover:border-amber-300 transition-colors">
        <p class="text-xs font-medium text-slate-500">Belum Disurvei</p>
        <p class="text-2xl font-semibold text-slate-900"><?= (int) ($belumDisurvei ?? 0) ?></p>
      </a>

      <!-- Menunggu SC -->
      <a href="/ipsrs/lk?status=Dalam+Perbaikan"
         class="group flex items-start flex-col gap-2 p-4 rounded-lg bg-slate-50 border border-slate-200 hover:border-red-300 transition-colors">
        <p class="text-xs font-medium text-slate-500">Menunggu Suku Cadang</p>
        <p class="text-2xl font-semibold text-slate-900"><?= (int) ($mnggSC ?? 0) ?></p>
      </a>

      <!-- PM Terlambat -->
      <a href="/ipsrs/preventif?status=Terlambat"
         class="group flex items-start flex-col gap-2 p-4 rounded-lg bg-slate-50 border border-slate-200 hover:border-red-300 transition-colors">
        <p class="text-xs font-medium text-slate-500">PM Terlambat</p>
        <p class="text-2xl font-semibold text-slate-900"><?= (int) ($pmTerlambat ?? 0) ?></p>
      </a>

      <!-- Stok Menipis -->
      <a href="/ipsrs/stok?status=Menipis"
         class="group flex items-start flex-col gap-2 p-4 rounded-lg bg-slate-50 border border-slate-200 hover:border-orange-300 transition-colors">
        <p class="text-xs font-medium text-slate-500">Stok Menipis</p>
        <p class="text-2xl font-semibold text-slate-900"><?= (int) ($stokMenipis ?? 0) ?></p>
      </a>

      <!-- Selesai Hari Ini -->
      <a href="/ipsrs/lk?status=Selesai"
         class="group flex items-start flex-col gap-2 p-4 rounded-lg bg-slate-50 border border-slate-200 hover:border-emerald-300 transition-colors xl:col-span-2">
        <p class="text-xs font-medium text-slate-500">Pekerjaan Selesai Hari Ini</p>
        <p class="text-2xl font-semibold text-slate-900"><?= (int) ($selesaiHariIni ?? 0) ?></p>
      </a>

    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════════════════════
     PIPELINE + JADWAL — Side by Side
     ════════════════════════════════════════════════════════════════════════ -->
<!-- ════════════════════════════════════════════════════════════════════════
     PIPELINE + JADWAL — Side by Side
     ════════════════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

  <!-- Pipeline Chart -->
  <div class="card p-5">
    <h2 class="text-sm font-semibold text-slate-800 mb-5">Pipeline Perbaikan</h2>
    <div class="relative h-48 w-full">
      <canvas id="pipelineChart"></canvas>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const ctx = document.getElementById('pipelineChart');
      if (ctx) {
        <?php
          $labels = [];
          $data = [];
          $colors = [];
          foreach (($pipeline ?? []) as $bar) {
            $labels[] = $bar['label'];
            $data[] = (int) $bar['count'];
            
            // Map tailwind color classes to hex for Chart.js (flat enterprise colors)
            $bg = '#cbd5e1';
            if (str_contains($bar['color'], 'indigo') || str_contains($bar['color'], 'red')) $bg = '#c62828';
            if (str_contains($bar['color'], 'amber')) $bg = '#eab308';
            if (str_contains($bar['color'], 'blue')) $bg = '#3b82f6';
            if (str_contains($bar['color'], 'emerald')) $bg = '#10b981';
            $colors[] = $bg;
          }
        ?>
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
              label: 'Jumlah',
              data: <?= json_encode($data) ?>,
              backgroundColor: <?= json_encode($colors) ?>,
              borderRadius: 4,
              barThickness: 24
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
              y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1, color: '#64748b', font: {family: 'Inter'} } },
              x: { grid: { display: false }, ticks: { color: '#64748b', font: {family: 'Inter'} } }
            }
          }
        });
      }
    });
  </script>

  <!-- Upcoming Jadwal -->
  <div class="card p-5">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-sm font-semibold text-slate-800">Lembar Preventif Mendatang</h2>
      <a href="/ipsrs/preventif" class="text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors">Lihat semua &rarr;</a>
    </div>
    <?php if (empty($upcoming)): ?>
    <div class="flex flex-col items-center justify-center py-8 text-center">
      <p class="text-sm text-slate-500">Tidak ada jadwal mendatang</p>
    </div>
    <?php else: ?>
    <div class="space-y-3">
      <?php foreach ($upcoming as $j): ?>
      <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100 last:border-0 last:pb-0">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded bg-slate-50 border border-slate-200 flex flex-col items-center justify-center shrink-0">
            <span class="text-xs font-semibold text-slate-700 leading-none"><?= tgl($j['tanggal'], 'd') ?></span>
            <span class="text-[9px] text-slate-500 uppercase leading-none mt-1"><?= tgl($j['tanggal'], 'M') ?></span>
          </div>
          <div class="min-w-0">
            <p class="text-sm font-medium text-slate-900 truncate"><?= esc($j['aset'] ?? $j['nama_aset'] ?? '-') ?></p>
            <p class="text-xs text-slate-500 truncate"><?= esc($j['lokasi'] ?? '-') ?></p>
          </div>
        </div>
        <div class="text-right shrink-0">
          <p class="text-xs font-medium text-slate-700"><?= esc($j['teknisi'] ?? '-') ?></p>
          <p class="text-xs text-slate-400"><?= esc($j['jam'] ?? '-') ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════════════════════
     RECENT LK TABLE
     ════════════════════════════════════════════════════════════════════════ -->
<div class="card overflow-hidden">
  <div class="flex items-center justify-between p-5 border-b border-slate-100">
    <h2 class="text-sm font-semibold text-slate-800">Laporan Kerusakan Terbaru</h2>
    <a href="/ipsrs/lk" class="text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors">Lihat semua &rarr;</a>
  </div>

  <?php if (empty($recentLK)): ?>
  <div class="flex flex-col items-center justify-center py-12 text-center">
    <p class="text-sm text-slate-500">Belum ada laporan kerusakan</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr>
          <th class="py-3 px-5 border-b border-slate-200 bg-slate-50 text-xs font-medium text-slate-500 uppercase tracking-wider">No. Order</th>
          <th class="py-3 px-5 border-b border-slate-200 bg-slate-50 text-xs font-medium text-slate-500 uppercase tracking-wider">Keluhan</th>
          <th class="py-3 px-5 border-b border-slate-200 bg-slate-50 text-xs font-medium text-slate-500 uppercase tracking-wider">Pelapor</th>
          <th class="py-3 px-5 border-b border-slate-200 bg-slate-50 text-xs font-medium text-slate-500 uppercase tracking-wider">Lokasi</th>
          <th class="py-3 px-5 border-b border-slate-200 bg-slate-50 text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($recentLK as $lk): ?>
        <?php
          $s = $lk['status'] ?? '';
          $sBadge = status_lk_badge($s);
        ?>
        <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="window.location.href='/ipsrs/lk/<?= esc($lk['id'] ?? '') ?>'">
          <td class="py-3 px-5">
            <span class="font-mono text-xs font-medium text-slate-900 group-hover:text-red-700 transition-colors"><?= esc($lk['no_order'] ?? '-') ?></span>
          </td>
          <td class="py-3 px-5 max-w-[240px]">
            <p class="text-sm text-slate-700 truncate"><?= esc($lk['keluhan'] ?? '-') ?></p>
          </td>
          <td class="py-3 px-5 text-sm text-slate-600"><?= esc($lk['pelapor'] ?? '-') ?></td>
          <td class="py-3 px-5 text-sm text-slate-600"><?= esc($lk['lokasi'] ?? '-') ?></td>
          <td class="py-3 px-5"><span class="<?= $sBadge ?>"><?= esc($s) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

