<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl mx-auto">
  <!-- Header / Greeting -->
  <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl p-8 text-white shadow-lg relative overflow-hidden">
    <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
      <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13.5h-13L12 6.5z"/></svg>
    </div>
    
    <div class="relative z-10 flex flex-col md:flex-row gap-6 justify-between items-start md:items-center">
      <div>
        <p class="text-indigo-100 font-medium mb-1"><?= date('l, d F Y') ?></p>
        <h1 class="text-3xl font-bold">Halo, <?= esc(session('user_name')) ?>! 👋</h1>
        <p class="mt-2 text-indigo-100 max-w-md text-sm leading-relaxed">
          Ini adalah dashboard khusus Pelapor. Anda dapat melihat ringkasan status laporan kerusakan yang telah Anda buat untuk unit/ruangan Anda.
        </p>
      </div>
      
      <div class="shrink-0">
        <a href="/ipsrs/aset" class="inline-flex items-center gap-2 px-6 py-3 bg-[#121620]/60 text-[#CCFF00] hover:bg-indigo-50 font-bold rounded-xl transition-all shadow-[0_4px_20px_rgba(0,0,0,0.5)]">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Lapor Kerusakan Baru
        </a>
      </div>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-[#121620]/60 rounded-3xl p-6 shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-white/5 flex items-center gap-5">
      <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Sedang Diproses</p>
        <div class="flex items-end gap-2">
          <h3 class="text-3xl font-black text-gray-100 leading-none"><?= esc($totalActive) ?></h3>
          <span class="text-sm font-medium text-gray-400 mb-1">Laporan</span>
        </div>
      </div>
    </div>

    <div class="bg-[#121620]/60 rounded-3xl p-6 shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-white/5 flex items-center gap-5">
      <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Selesai Ditangani</p>
        <div class="flex items-end gap-2">
          <h3 class="text-3xl font-black text-gray-100 leading-none"><?= esc($totalDone) ?></h3>
          <span class="text-sm font-medium text-gray-400 mb-1">Laporan</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Reports -->
  <div class="bg-[#121620]/60 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-white/5 overflow-hidden">
    <div class="px-6 py-5 border-b border-white/5 flex items-center justify-between">
      <h2 class="font-bold text-gray-100">5 Laporan Terakhir Anda</h2>
      <a href="/ipsrs/lk" class="text-sm font-bold text-[#CCFF00] hover:text-indigo-700">Lihat Semua →</a>
    </div>
    
    <?php if (empty($recentLK)): ?>
      <div class="p-8 text-center text-gray-400">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        <p>Anda belum memiliki riwayat laporan kerusakan.</p>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">No. Laporan</th>
              <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Tanggal</th>
              <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Aset / Keluhan</th>
              <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <?php foreach ($recentLK as $lk): 
                // Determine badge style
                $badgeCls = 'bg-[#202532] text-gray-200';
                if ($lk['status'] === \App\Config\IPSRS::STATUS_LK[0]) $badgeCls = 'bg-red-50 text-red-600 ring-red-500/20'; // Belum disurvei
                if (in_array($lk['status'], [\App\Config\IPSRS::STATUS_LK[1], \App\Config\IPSRS::STATUS_LK[2]])) $badgeCls = 'bg-[#CCFF00]/10 text-[#CCFF00] ring-indigo-500/20'; // Disposisi/Survei
                if (in_array($lk['status'], [\App\Config\IPSRS::STATUS_LK[3], \App\Config\IPSRS::STATUS_LK[4], \App\Config\IPSRS::STATUS_LK[5]])) $badgeCls = 'bg-amber-50 text-amber-600 ring-amber-500/20'; // Perbaikan/Mngg
                if ($lk['status'] === \App\Config\IPSRS::STATUS_LK[6]) $badgeCls = 'bg-emerald-50 text-emerald-600 ring-emerald-500/20'; // Selesai
            ?>
              <tr class="hover:bg-gray-50/30 transition-colors group cursor-pointer" onclick="window.location.href='/ipsrs/lk/<?= esc($lk['id'] ?? '') ?>'">
                <td class="px-6 py-4">
                  <span class="font-mono text-xs font-bold text-gray-300 group-hover:text-indigo-600 transition-colors"><?= esc($lk['no_order'] ?? '') ?></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <p class="text-sm font-semibold text-gray-100"><?= esc(date('d M Y', strtotime($lk['tanggal'] ?? date('Y-m-d')))) ?></p>
                  <p class="text-xs text-gray-400"><?= esc(date('H:i', strtotime($lk['jam_laporan'] ?? date('H:i:s')))) ?></p>
                </td>
                <td class="px-6 py-4">
                  <p class="text-sm font-bold text-gray-100 line-clamp-1"><?= esc($lk['nama_aset'] ?? '') ?></p>
                  <p class="text-xs text-gray-400 line-clamp-1 mt-0.5"><?= esc($lk['keluhan'] ?? '') ?></p>
                </td>
                <td class="px-6 py-4 text-right">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset <?= $badgeCls ?>">
                    <?= esc($lk['status'] ?? 'UNKNOWN') ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>
