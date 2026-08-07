<?php
$total = count($lk ?? []);
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan Kerusakan</h1>
    <p class="text-sm text-slate-500 mt-1">Kelola dan pantau laporan kerusakan aset</p>
  </div>
  <a href="/ipsrs/lk/baru"
     class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
    </svg>
    Buat LK Baru
  </a>
</div>

<!-- Filter Bar -->
<div class="card p-4 mb-6">
  <form method="GET" action="/ipsrs/lk" class="flex flex-wrap gap-4 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Cari</label>
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35" stroke-linecap="round" stroke-width="2"/>
        </svg>
        <input type="text" name="q" value="<?= esc($search ?? '') ?>"
               placeholder="No. order, keluhan, pelapor..."
               class="w-full pl-9 pr-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 focus:border-red-600 transition-colors shadow-sm">
      </div>
    </div>
    <div class="min-w-[170px]">
      <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Status</label>
      <select name="status" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 focus:border-red-600 transition-colors shadow-sm">
        <option value="">Semua Status</option>
        <?php foreach (['Laporan Masuk', 'Didisposisi', 'Survei', 'Menunggu Suku Cadang', 'Dalam Perbaikan', 'Selesai'] as $opt): ?>
        <option value="<?= $opt ?>" <?= ($status ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="min-w-[140px]">
      <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kode</label>
      <select name="kode" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 focus:border-red-600 transition-colors shadow-sm">
        <option value="">Semua Kode</option>
        <?php foreach (($kodeKerusakan ?? []) as $kk): ?>
        <option value="<?= esc($kk['kode'] ?? '') ?>" <?= ($kode ?? '') === ($kk['kode'] ?? '') ? 'selected' : '' ?>><?= esc($kk['kode'] ?? '') ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit"
            class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
      Filter
    </button>
    <?php if (!empty($search) || !empty($status) || !empty($kode)): ?>
    <a href="/ipsrs/lk" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- Count -->
<p class="text-sm text-slate-500 mb-4">Menampilkan <span class="font-semibold text-slate-900"><?= $total ?></span> laporan</p>

<!-- Table -->
<div class="card overflow-hidden">
  <?php if (empty($lk)): ?>
  <div class="p-12 text-center flex flex-col items-center justify-center">
    <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mb-4">
      <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
    </div>
    <h3 class="text-lg font-bold text-slate-800 mb-1">Belum Ada Laporan</h3>
    <p class="text-sm text-slate-500 max-w-sm mx-auto mb-6">Saat ini tidak ada laporan kerusakan yang tercatat. Silakan buat laporan baru jika ada masalah.</p>
    <a href="/ipsrs/lk/baru" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white text-sm font-medium rounded-md transition-colors shadow-sm inline-flex items-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Buat Laporan
    </a>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table id="tabel-data" class="w-full text-left border-collapse">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-5 text-xs font-medium text-slate-500 uppercase tracking-wider">No. Order</th>
          <th class="py-3 px-5 text-xs font-medium text-slate-500 uppercase tracking-wider">Keluhan</th>
          <th class="py-3 px-5 text-xs font-medium text-slate-500 uppercase tracking-wider">Pelapor / Unit</th>
          <th class="py-3 px-5 text-xs font-medium text-slate-500 uppercase tracking-wider">Lokasi</th>
          <th class="py-3 px-5 text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
          <th class="py-3 px-5 text-xs font-medium text-slate-500 uppercase tracking-wider">Respon</th>
          <th class="py-3 px-5 text-xs font-medium text-slate-500 uppercase tracking-wider text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($lk as $item): ?>
        <?php
          $s = $item['status'] ?? '';
          $sBadge = status_lk_badge($s);
          $rt = (int)($item['response_time'] ?? 0);
          $rtClass = $rt > 15 ? 'font-semibold text-red-600' : 'text-slate-600';
        ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="py-3 px-5">
            <span class="font-mono text-xs text-slate-900 font-medium"><?= esc($item['no_order'] ?? '-') ?></span>
          </td>
          <td class="py-3 px-5 max-w-[220px]">
            <a href="/ipsrs/lk/<?= esc($item['id'] ?? '') ?>" class="text-sm text-red-700 hover:text-red-800 font-medium truncate block transition-colors">
              <?= esc($item['keluhan'] ?? '-') ?>
            </a>
          </td>
          <td class="py-3 px-5">
            <p class="text-sm text-slate-700 font-medium"><?= esc($item['pelapor'] ?? '-') ?></p>
            <p class="text-xs text-slate-400"><?= esc($item['unit_pelapor'] ?? '') ?></p>
          </td>
          <td class="py-3 px-5 text-sm text-slate-600"><?= esc($item['lokasi'] ?? '-') ?></td>
          <td class="py-3 px-5"><span class="<?= $sBadge ?>"><?= esc($s) ?></span></td>
          <td class="py-3 px-5 text-sm <?= $rtClass ?>"><?= $rt > 0 ? $rt.' mnt' : '-' ?></td>
          <td class="py-3 px-5 text-right">
            <form action="/ipsrs/lk/<?= esc($item['id'] ?? '') ?>/delete" method="POST" class="inline-block" onsubmit="confirmFormSubmit(event, this, 'Semua data terkait (suku cadang, dsb) akan ikut terhapus!');">
              <?= csrf_field() ?>
              <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Hapus LK">
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

<script>
$(document).ready(function() {
    if ($('#tabel-data').length) {
        $('#tabel-data').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
            pageLength: 25,
            ordering: false
        });
    }
});
</script>
