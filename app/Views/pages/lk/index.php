<?php
$total = count($lk ?? []);
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-100">Laporan Kerusakan</h1>
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
      <label class="block text-xs font-medium text-gray-400 mb-1.5">Cari</label>
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35" stroke-linecap="round"/>
        </svg>
        <input type="text" name="q" value="<?= esc($search ?? '') ?>"
               placeholder="No. order, keluhan, pelapor..."
               class="w-full pl-9 pr-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>
    </div>
    <div class="min-w-[170px]">
      <label class="block text-xs font-medium text-gray-400 mb-1.5">Status</label>
      <select name="status" class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
        <option value="">Semua Status</option>
        <?php foreach (['Laporan Masuk', 'Didisposisi', 'Survei', 'Menunggu Suku Cadang', 'Dalam Perbaikan', 'Selesai'] as $opt): ?>
        <option value="<?= $opt ?>" <?= ($status ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="min-w-[140px]">
      <label class="block text-xs font-medium text-gray-400 mb-1.5">Kode</label>
      <select name="kode" class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
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
    <a href="/ipsrs/lk" class="px-5 py-2.5 text-sm font-semibold text-gray-400 hover:text-gray-700 rounded-xl bg-[#202532] hover:bg-white/15 transition-colors">Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- Count -->
<p class="text-sm text-gray-400 mb-3">Menampilkan <span class="font-semibold text-gray-200"><?= $total ?></span> laporan</p>

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
          <th class="text-right px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($lk as $item): ?>
        <?php
          $s = $item['status'] ?? '';
          $sBadge = status_lk_badge($s);
          $rt = (int)($item['response_time'] ?? 0);
          $rtClass = $rt > 15 ? 'font-semibold text-red-600' : 'text-gray-300';
        ?>
        <tr class="hover:bg-indigo-50/40 transition-colors group cursor-pointer">
          <td class="px-5 py-3.5" onclick="window.location='/ipsrs/lk/<?= esc($item['id'] ?? '') ?>'">
            <span class="font-mono text-xs text-[#CCFF00] font-semibold"><?= esc($item['no_order'] ?? '-') ?></span>
          </td>
          <td class="px-5 py-3.5 max-w-[220px]" onclick="window.location='/ipsrs/lk/<?= esc($item['id'] ?? '') ?>'">
            <a href="/ipsrs/lk/<?= esc($item['id'] ?? '') ?>" class="text-gray-100 hover:text-indigo-600 font-medium truncate block transition-colors">
              <?= esc($item['keluhan'] ?? '-') ?>
            </a>
          </td>
          <td class="px-5 py-3.5" onclick="window.location='/ipsrs/lk/<?= esc($item['id'] ?? '') ?>'">
            <p class="text-gray-200 font-medium"><?= esc($item['pelapor'] ?? '-') ?></p>
            <p class="text-xs text-gray-400"><?= esc($item['unit_pelapor'] ?? '') ?></p>
          </td>
          <td class="px-5 py-3.5 text-gray-300" onclick="window.location='/ipsrs/lk/<?= esc($item['id'] ?? '') ?>'"><?= esc($item['lokasi'] ?? '-') ?></td>
          <td class="px-5 py-3.5" onclick="window.location='/ipsrs/lk/<?= esc($item['id'] ?? '') ?>'"><span class="<?= $sBadge ?>"><?= esc($s) ?></span></td>
          <td class="px-5 py-3.5 <?= $rtClass ?>" onclick="window.location='/ipsrs/lk/<?= esc($item['id'] ?? '') ?>'"><?= $rt > 0 ? $rt.' mnt' : '-' ?></td>
          <td class="px-5 py-3.5 text-right">
            <form action="/ipsrs/lk/<?= esc($item['id'] ?? '') ?>/delete" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Laporan Kerusakan ini? Semua data terkait (suku cadang, dsb) akan ikut terhapus!');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-500 hover:text-red-700 p-1 rounded-md hover:bg-red-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
