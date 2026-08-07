
<div class="space-y-6 max-w-5xl mx-auto">
  <!-- Header / Greeting -->
  <div class="bg-red-700 rounded-md p-8 text-white shadow-sm relative overflow-hidden">
    <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
      <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13.5h-13L12 6.5z"/></svg>
    </div>
    
    <div class="relative z-10 flex flex-col md:flex-row gap-6 justify-between items-start md:items-center">
      <div>
        <p class="text-red-100 font-medium mb-1"><?= date('l, d F Y') ?></p>
        <h1 class="text-3xl font-bold tracking-tight">Halo, <?= esc(session('user_name')) ?>!</h1>
        <p class="mt-2 text-red-100 max-w-md text-sm leading-relaxed">
          Ini adalah dashboard khusus Pelapor. Anda dapat melihat ringkasan status laporan kerusakan yang telah Anda buat untuk unit/ruangan Anda.
        </p>
      </div>
      
      <div class="shrink-0">
        <a href="/ipsrs/aset" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white text-red-700 hover:bg-slate-50 font-semibold rounded-md transition-all shadow-sm">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Lapor Kerusakan Baru
        </a>
      </div>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-md p-6 border border-slate-200 shadow-sm flex items-center gap-5">
      <div class="w-12 h-12 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div>
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Sedang Diproses</p>
        <div class="flex items-end gap-2">
          <h3 class="text-3xl font-bold text-slate-900 leading-none"><?= esc($totalActive) ?></h3>
          <span class="text-sm font-medium text-slate-500 mb-1">Laporan</span>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-md p-6 border border-slate-200 shadow-sm flex items-center gap-5">
      <div class="w-12 h-12 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div>
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Selesai Ditangani</p>
        <div class="flex items-end gap-2">
          <h3 class="text-3xl font-bold text-slate-900 leading-none"><?= esc($totalDone) ?></h3>
          <span class="text-sm font-medium text-slate-500 mb-1">Laporan</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Reports -->
  <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
      <h2 class="text-sm font-bold text-slate-800">5 Laporan Terakhir Anda</h2>
      <a href="/ipsrs/lk" class="text-xs font-medium text-red-700 hover:text-red-800 transition-colors">Lihat Semua &rarr;</a>
    </div>
    
    <?php if (empty($recentLK)): ?>
      <div class="p-8 text-center text-slate-500">
        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        <p class="text-sm">Anda belum memiliki riwayat laporan kerusakan.</p>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
              <th class="px-6 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">No. Laporan</th>
              <th class="px-6 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
              <th class="px-6 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Aset / Keluhan</th>
              <th class="px-6 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider text-right">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php foreach ($recentLK as $lk): 
                $sBadge = status_lk_badge($lk['status'] ?? '');
            ?>
              <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="window.location.href='/ipsrs/lk/<?= esc($lk['id'] ?? '') ?>'">
                <td class="px-6 py-4">
                  <span class="font-mono text-xs font-medium text-slate-900 group-hover:text-red-700 transition-colors"><?= esc($lk['no_order'] ?? '') ?></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <p class="text-sm font-medium text-slate-900"><?= esc(date('d M Y', strtotime($lk['tanggal'] ?? date('Y-m-d')))) ?></p>
                  <p class="text-xs text-slate-500 mt-0.5"><?= esc(date('H:i', strtotime($lk['jam_laporan'] ?? date('H:i:s')))) ?></p>
                </td>
                <td class="px-6 py-4">
                  <p class="text-sm font-medium text-slate-900 line-clamp-1"><?= esc($lk['nama_aset'] ?? '') ?></p>
                  <p class="text-xs text-slate-500 line-clamp-1 mt-0.5"><?= esc($lk['keluhan'] ?? '') ?></p>
                </td>
                <td class="px-6 py-4 text-right">
                  <span class="<?= $sBadge ?>">
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


