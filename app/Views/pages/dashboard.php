<?php
$today = $today ?? date('Y-m-d');
$hour = (int) date('H');
$sapa = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
$firstName = explode(' ', session('user_name') ?? 'Admin')[0];
?>

<!-- ════════════════════════════════════════════════════════════════════════
     HERO BANNER — Gradient + Greeting + KPI
     ════════════════════════════════════════════════════════════════════════ -->
<div class="rounded-2xl p-6 md:p-8 mb-8 text-white relative overflow-hidden stagger-1"
     style="background: linear-gradient(135deg, #0A0F1B 0%, #151A29 100%); box-shadow: inset 0 0 40px rgba(0, 240, 255, 0.05); border: 1px solid rgba(0, 240, 255, 0.1);">

  <!-- Decorative circles -->
  <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-[#CCFF00]/5 blur-2xl"></div>
  <div class="absolute -bottom-20 -left-20 w-48 h-48 rounded-full bg-[#00F0FF]/5 blur-2xl"></div>

  <!-- Greeting -->
  <div class="relative z-10 mb-6">
    <p class="text-[#00F0FF] text-xs font-semibold tracking-widest uppercase mb-1"><?= tgl($today, 'l, d F Y') ?></p>
    <h1 class="font-display text-2xl md:text-4xl font-bold leading-tight"><?= esc($sapa) ?>, <?= esc($firstName) ?> 👋</h1>
  </div>

  <!-- KPI Cards — Glass Morphism -->
  <div class="relative z-10 grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
    <!-- SLA -->
    <div class="bg-black/30 backdrop-blur-sm rounded-xl p-4 border border-white/5 shadow-inner">
      <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2">SLA Respon</p>
      <p class="font-display text-3xl font-bold leading-none text-white"><?= number_format($slaPct ?? 0, 1) ?><span class="text-lg text-gray-500 font-medium">%</span></p>
      <div class="mt-3 h-1 rounded-full bg-white/10">
        <div class="h-1 rounded-full bg-[#CCFF00]" style="width:<?= min(100, (float) ($slaPct ?? 0)) ?>%; box-shadow: 0 0 10px #CCFF00;"></div>
      </div>
      <p class="text-gray-400 text-[11px] mt-1.5">&lt;15 menit</p>
    </div>

    <!-- Rata-rata Respon -->
    <div class="bg-black/30 backdrop-blur-sm rounded-xl p-4 border border-white/5 shadow-inner">
      <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2">Rata-rata Respon</p>
      <p class="font-display text-3xl font-bold leading-none text-white"><?= number_format($avgRespon ?? 0, 0) ?><span class="text-lg text-gray-500 font-medium"> mnt</span></p>
      <div class="mt-3 flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5 text-[#00F0FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-gray-400 text-[11px]">Waktu respons</p>
      </div>
    </div>

    <!-- PM Progress -->
    <div class="bg-black/30 backdrop-blur-sm rounded-xl p-4 border border-white/5 shadow-inner">
      <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2">Preventif</p>
      <p class="font-display text-3xl font-bold leading-none text-white"><?= (int) ($jadwalSelesai ?? 0) ?><span class="text-lg text-gray-500 font-medium"> / <?= (int) ($jadwalTotal ?? 0) ?></span></p>
      <div class="mt-3 h-1 rounded-full bg-white/10">
        <div class="h-1 rounded-full bg-[#00F0FF]" style="width:<?= min(100, (float) ($pmPct ?? 0)) ?>%; box-shadow: 0 0 10px #00F0FF;"></div>
      </div>
      <p class="text-gray-400 text-[11px] mt-1.5"><?= number_format($pmPct ?? 0, 0) ?>% selesai</p>
    </div>

    <!-- LK Aktif -->
    <div class="bg-black/30 backdrop-blur-sm rounded-xl p-4 border border-white/5 shadow-inner">
      <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2">LK Aktif</p>
      <p class="font-display text-3xl font-bold leading-none text-white"><?= (int) ($lkAktif ?? 0) ?></p>
      <div class="mt-3 flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-[#FF3366] animate-pulse shadow-[0_0_8px_#FF3366]"></span>
        <p class="text-gray-400 text-[11px]">Sedang dikerjakan</p>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════════════════════
     PRIORITY + STATUS OVERVIEW — Side by Side
     ════════════════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

  <!-- Priority Alert Stack (1/3 width) -->
  <?php if (!empty($priority)): ?>
  <div class="card p-5 stagger-2">
    <div class="flex items-center gap-2 mb-4">
      <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
      </div>
      <h2 class="text-sm font-semibold text-gray-100">Perlu Ditindak</h2>
    </div>
    <div class="space-y-2.5">
      <?php foreach ($priority as $p): ?>
      <?php $accent = ($p['level'] === 'critical') ? 'border-red-400 bg-red-50/80' : 'border-amber-400 bg-amber-50/80'; ?>
      <div class="flex items-center justify-between gap-3 pl-3 pr-3 py-2.5 rounded-xl border-l-4 <?= $accent ?>">
        <div class="min-w-0">
          <p class="text-[13px] font-semibold text-gray-100 leading-snug"><?= esc($p['title'] ?? '') ?></p>
          <p class="text-[11px] text-gray-400 mt-0.5 truncate"><?= esc($p['desc'] ?? '') ?></p>
        </div>
        <a href="<?= esc($p['path'] ?? '#') ?>"
           class="shrink-0 text-[11px] font-medium px-2.5 py-1 rounded-lg bg-[#121620]/60 border border-white/10 text-gray-200 hover:bg-white/5 transition-colors">
          <?= esc($p['action'] ?? 'Lihat') ?>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php else: ?>
  <div class="card p-5 flex flex-col items-center justify-center text-center stagger-2">
    <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center mb-3">
      <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
    </div>
    <p class="text-sm font-medium text-gray-300">Semua aman</p>
    <p class="text-xs text-gray-400 mt-0.5">Tidak ada item prioritas</p>
  </div>
  <?php endif; ?>

  <!-- Status Overview (2/3 width) -->
  <div class="lg:col-span-2 card p-5">
    <h2 class="text-sm font-semibold text-gray-100 mb-4">Status Operasional</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-3">
      <a href="/ipsrs/lk?status=Laporan+Masuk"
         class="group flex items-center gap-4 p-5 rounded-2xl bg-white/60 hover:bg-white hover:shadow-xl hover:shadow-amber-500/10 hover:-translate-y-1 border border-white/50 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-inner">
          <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-2xl font-bold text-gray-100 leading-none"><?= (int) ($belumDisurvei ?? 0) ?></p>
          <p class="text-[11px] text-gray-400 mt-1">Belum Disurvei</p>
        </div>
      </a>

      <a href="/ipsrs/lk?status=Dalam+Perbaikan"
         class="group flex items-center gap-4 p-5 rounded-2xl bg-white/60 hover:bg-white hover:shadow-xl hover:shadow-indigo-500/10 hover:-translate-y-1 border border-white/50 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-inner">
          <svg class="w-6 h-6 text-[#CCFF00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
        </div>
        <div>
          <p class="text-2xl font-bold text-gray-100 leading-none"><?= (int) ($mnggSC ?? 0) ?></p>
          <p class="text-[11px] text-gray-400 mt-1">Menunggu SC</p>
        </div>
      </a>

      <a href="/ipsrs/preventif?status=Terlambat"
         class="group flex items-center gap-4 p-5 rounded-2xl bg-white/60 hover:bg-white hover:shadow-xl hover:shadow-red-500/10 hover:-translate-y-1 border border-white/50 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-inner">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-2xl font-bold text-gray-100 leading-none"><?= (int) ($pmTerlambat ?? 0) ?></p>
          <p class="text-[11px] text-gray-400 mt-1">PM Terlambat</p>
        </div>
      </a>

      <a href="/ipsrs/stok?status=Menipis"
         class="group flex items-center gap-4 p-5 rounded-2xl bg-white/60 hover:bg-white hover:shadow-xl hover:shadow-orange-500/10 hover:-translate-y-1 border border-white/50 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-inner">
          <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div>
          <p class="text-2xl font-bold text-gray-100 leading-none"><?= (int) ($stokMenipis ?? 0) ?></p>
          <p class="text-[11px] text-gray-400 mt-1">Stok Menipis</p>
        </div>
      </a>

      <a href="/ipsrs/lk?status=Selesai"
         class="group flex items-center gap-4 p-5 rounded-2xl bg-white/60 hover:bg-white hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-1 border border-white/50 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-inner">
          <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-2xl font-bold text-gray-100 leading-none"><?= (int) ($selesaiHariIni ?? 0) ?></p>
          <p class="text-[11px] text-gray-400 mt-1">Selesai Hari Ini</p>
        </div>
      </a>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════════════════════
     PIPELINE + JADWAL — Side by Side
     ════════════════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

  <!-- Pipeline Chart -->
  <div class="card p-5 stagger-3">
    <h2 class="text-sm font-semibold text-gray-100 mb-5">Pipeline Perbaikan</h2>
    <?php $pMax = max(1, (int) ($pipelineMax ?? 1)); ?>
    <div class="flex items-end gap-2.5 h-36">
      <?php foreach (($pipeline ?? []) as $bar): ?>
      <?php
        $barH = max(6, round(((int) $bar['count'] / $pMax) * 100));
        $barColor = esc($bar['color'] ?? 'bg-indigo-400');
      ?>
      <div class="flex-1 flex flex-col items-center gap-1.5">
        <span class="text-xs font-bold text-gray-200"><?= (int) $bar['count'] ?></span>
        <div class="w-full rounded-t-lg <?= $barColor ?> transition-all" style="height:<?= $barH ?>%"></div>
        <span class="text-[10px] text-gray-400 text-center leading-tight"><?= esc($bar['label'] ?? '') ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Upcoming Jadwal -->
  <div class="card p-5 stagger-3">
    <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
      <h2 class="text-sm font-semibold text-gray-100">Lembar Preventif Mendatang</h2>
      <a href="/ipsrs/preventif" class="text-[11px] font-bold text-[#CCFF00] hover:text-indigo-800 hover:underline">Lihat semua →</a>
    </div>
    <?php if (empty($upcoming)): ?>
    <div class="flex flex-col items-center justify-center py-8 text-center">
      <div class="w-12 h-12 rounded-full bg-[#202532] flex items-center justify-center mb-3">
        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <p class="text-sm text-gray-400">Tidak ada jadwal mendatang</p>
    </div>
    <?php else: ?>
    <div class="space-y-2">
      <?php foreach ($upcoming as $j): ?>
      <div class="flex items-center justify-between gap-3 px-3.5 py-3 rounded-xl bg-[#181C25]/80 hover:bg-white/10 transition-colors">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-[#CCFF00]/10 flex flex-col items-center justify-center shrink-0">
            <span class="text-[11px] font-bold text-indigo-700 leading-none"><?= tgl($j['tanggal'], 'd') ?></span>
            <span class="text-[9px] text-indigo-500 uppercase leading-none mt-0.5"><?= tgl($j['tanggal'], 'M') ?></span>
          </div>
          <div class="min-w-0">
            <p class="text-sm font-medium text-gray-100 truncate"><?= esc($j['aset'] ?? $j['nama_aset'] ?? '-') ?></p>
            <p class="text-xs text-gray-400 truncate"><?= esc($j['lokasi'] ?? '-') ?></p>
          </div>
        </div>
        <div class="text-right shrink-0">
          <p class="text-xs font-medium text-gray-300"><?= esc($j['teknisi'] ?? '-') ?></p>
          <p class="text-[11px] text-gray-400"><?= esc($j['jam'] ?? '-') ?></p>
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
<div class="card stagger-4">
  <div class="flex items-center justify-between p-5 pb-0">
    <h2 class="text-sm font-semibold text-gray-100">Laporan Kerusakan Terbaru</h2>
    <a href="/ipsrs/lk" class="text-xs text-[#CCFF00] hover:text-indigo-700 font-medium hover:underline">Lihat semua →</a>
  </div>

  <?php if (empty($recentLK)): ?>
  <div class="flex flex-col items-center justify-center py-12 text-center">
    <div class="w-14 h-14 rounded-full bg-[#202532] flex items-center justify-center mb-3">
      <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <p class="text-sm text-gray-400">Belum ada laporan kerusakan</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-t border-white/5">
          <th class="text-left py-3 px-5 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">No. Order</th>
          <th class="text-left py-3 px-5 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Keluhan</th>
          <th class="text-left py-3 px-5 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Pelapor</th>
          <th class="text-left py-3 px-5 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Lokasi</th>
          <th class="text-left py-3 px-5 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Status</th>
          <th class="text-left py-3 px-5 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Respon</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($recentLK as $lk): ?>
        <?php
          $s = $lk['status'] ?? '';
          $sBadge = status_lk_badge($s);
          $rt = (int) ($lk['response_time'] ?? 0);
          $rtClass = $rt > 15 ? 'text-red-600 font-semibold' : 'text-gray-300';
        ?>
        <tr class="hover:bg-gray-50/50 transition-colors">
          <td class="py-3.5 px-5">
            <a href="/ipsrs/lk/<?= esc($lk['id'] ?? '') ?>" class="font-mono text-xs text-[#CCFF00] hover:text-indigo-700 hover:underline font-medium"><?= esc($lk['no_order'] ?? '-') ?></a>
          </td>
          <td class="py-3.5 px-5 max-w-[200px]">
            <p class="text-gray-100 truncate"><?= esc($lk['keluhan'] ?? '-') ?></p>
          </td>
          <td class="py-3.5 px-5 text-gray-300"><?= esc($lk['pelapor'] ?? '-') ?></td>
          <td class="py-3.5 px-5 text-gray-300"><?= esc($lk['lokasi'] ?? '-') ?></td>
          <td class="py-3.5 px-5"><span class="<?= $sBadge ?>"><?= esc($s) ?></span></td>
          <td class="py-3.5 px-5 <?= $rtClass ?>"><?= $rt > 0 ? $rt . ' mnt' : '-' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
