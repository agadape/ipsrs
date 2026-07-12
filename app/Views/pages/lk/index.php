<?php
$total = count($lk ?? []);
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-800">Laporan Kerusakan</h1>
    <p class="text-sm text-gray-400 mt-0.5">Kelola dan pantau laporan kerusakan aset</p>
  </div>
  <a href="/ipsrs/lk/baru"
     class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
    </svg>
    Buat LK Baru
  </a>
</div>

<!-- Filter Bar -->
<div class="card p-4 mb-5">
  <form method="GET" action="/ipsrs/lk" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari</label>
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35" stroke-linecap="round"/>
        </svg>
        <input type="text" name="q" value="<?= esc($search ?? '') ?>"
               placeholder="No. order, keluhan, pelapor..."
               class="w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>
    </div>
    <div class="min-w-[170px]">
      <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
      <select name="status" class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
        <option value="">Semua Status</option>
        <?php foreach (['Laporan Masuk', 'Didisposisi', 'Survei', 'Menunggu Suku Cadang', 'Dalam Perbaikan', 'Selesai'] as $opt): ?>
        <option value="<?= $opt ?>" <?= ($status ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="min-w-[140px]">
      <label class="block text-xs font-medium text-gray-500 mb-1.5">Kode</label>
      <select name="kode" class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
        <option value="">Semua Kode</option>
        <?php foreach (($kodeKerusakan ?? []) as $kk): ?>
        <option value="<?= esc($kk['kode'] ?? '') ?>" <?= ($kode ?? '') === ($kk['kode'] ?? '') ? 'selected' : '' ?>><?= esc($kk['kode'] ?? '') ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit"
            class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
      Filter
    </button>
    <?php if (!empty($search) || !empty($status) || !empty($kode)): ?>
    <a href="/ipsrs/lk" class="px-5 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors">Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- Count -->
<p class="text-sm text-gray-500 mb-3">Menampilkan <span class="font-semibold text-gray-700"><?= $total ?></span> laporan</p>

<!-- Table -->
<div class="card overflow-hidden">
  <?php if (empty($lk)): ?>
  <div class="text-center py-16">
    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
    </svg>
    <p class="text-sm text-gray-400">Tidak ada laporan ditemukan.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50/80 border-b border-gray-200/60">
        <tr>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">No. Order</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Keluhan</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Pelapor / Unit</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Lokasi</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Respon</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($lk as $item): ?>
        <?php
          $s = $item['status'] ?? '';
          $sBadge = status_lk_badge($s);
          $rt = (int)($item['response_time'] ?? 0);
          $rtClass = $rt > 15 ? 'font-semibold text-red-600' : 'text-gray-600';
        ?>
        <tr class="hover:bg-indigo-50/40 transition-colors group cursor-pointer">
          <td class="px-5 py-3.5">
            <span class="font-mono text-xs text-indigo-600 font-semibold"><?= esc($item['no_order'] ?? '-') ?></span>
          </td>
          <td class="px-5 py-3.5 max-w-[220px]">
            <a href="/ipsrs/lk/<?= esc($item['id'] ?? '') ?>" class="text-gray-800 hover:text-indigo-600 font-medium truncate block transition-colors">
              <?= esc($item['keluhan'] ?? '-') ?>
            </a>
          </td>
          <td class="px-5 py-3.5">
            <p class="text-gray-700 font-medium"><?= esc($item['pelapor'] ?? '-') ?></p>
            <p class="text-xs text-gray-400"><?= esc($item['unit_pelapor'] ?? '') ?></p>
          </td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($item['lokasi'] ?? '-') ?></td>
          <td class="px-5 py-3.5"><span class="<?= $sBadge ?>"><?= esc($s) ?></span></td>
          <td class="px-5 py-3.5 <?= $rtClass ?>"><?= $rt > 0 ? $rt.' mnt' : '-' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
