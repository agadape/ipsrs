<?php
$total = count($aset ?? []);
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-800">Daftar Aset</h1>
    <p class="text-sm text-gray-400 mt-0.5">Kelola inventaris aset rumah sakit</p>
  </div>
  <a href="/ipsrs/aset/tambah"
     class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
    </svg>
    Tambah Aset
  </a>
</div>

<!-- Filter Bar -->
<div class="card p-4 mb-5">
  <form method="GET" action="/ipsrs/aset" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari Aset</label>
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35" stroke-linecap="round"/>
        </svg>
        <input type="text" name="q" value="<?= esc($search ?? '') ?>"
               placeholder="Nama, ID, kode aset..."
               class="w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>
    </div>
    <div class="min-w-[160px]">
      <label class="block text-xs font-medium text-gray-500 mb-1.5">Jenis</label>
      <select name="jenis" class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
        <option value="">Semua Jenis</option>
        <option value="Sarana"      <?= ($jenis ?? '') === 'Sarana'      ? 'selected' : '' ?>>Sarana</option>
        <option value="Prasarana"   <?= ($jenis ?? '') === 'Prasarana'   ? 'selected' : '' ?>>Prasarana</option>
        <option value="Alat Non Medis" <?= ($jenis ?? '') === 'Alat Non Medis' ? 'selected' : '' ?>>Alat Non Medis</option>
      </select>
    </div>
    <div class="min-w-[160px]">
      <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
      <select name="status" class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
        <option value="">Semua Status</option>
        <option value="Aktif"   <?= ($status ?? '') === 'Aktif'   ? 'selected' : '' ?>>Aktif</option>
        <option value="Tidak Aktif" <?= ($status ?? '') === 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
        <option value="Rusak"   <?= ($status ?? '') === 'Rusak'   ? 'selected' : '' ?>>Rusak</option>
      </select>
    </div>
    <button type="submit"
            class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
      Filter
    </button>
    <?php if (!empty($search) || !empty($jenis) || !empty($status)): ?>
    <a href="/ipsrs/aset" class="px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors">Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- Count -->
<p class="text-sm text-gray-500 mb-3">Menampilkan <span class="font-semibold text-gray-700"><?= $total ?></span> aset</p>

<!-- Table -->
<div class="card overflow-hidden">
  <?php if (empty($aset)): ?>
  <div class="p-12 text-center flex flex-col items-center justify-center">
    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
      <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    </div>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Aset</h3>
    <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">Data aset saat ini kosong atau tidak ditemukan.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table id="tabel-data" class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">ID Aset</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Aset</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kategori</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Jenis</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lokasi</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($aset as $a): ?>
        <?php
          $st = $a['status'] ?? '';
          $stBadge = status_aset_badge($st);
          $jn = $a['jenis'] ?? '';
          $jnBadge = match($jn) {
            'Sarana'         => 'badge bg-indigo-50 text-indigo-700',
            'Prasarana'      => 'badge bg-slate-100 text-slate-600',
            'Alat Non Medis' => 'badge bg-gray-100 text-gray-600',
            default          => 'badge bg-gray-100 text-gray-500',
          };
        ?>
        <tr class="hover:bg-indigo-50/40 transition-colors group">
          <td class="px-5 py-3.5">
            <span class="font-mono text-xs text-indigo-600 font-semibold"><?= esc($a['nomor_aset'] ?? $a['id'] ?? '-') ?></span>
          </td>
          <td class="px-5 py-3.5">
            <a href="/ipsrs/aset/<?= esc($a['id'] ?? '') ?>" class="font-medium text-gray-800 hover:text-indigo-600 transition-colors">
              <?= esc($a['nama'] ?? '-') ?>
            </a>
          </td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($a['kategori'] ?? '-') ?></td>
          <td class="px-5 py-3.5"><span class="<?= $jnBadge ?>"><?= esc($jn ?: '-') ?></span></td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($a['lokasi'] ?? '-') ?></td>
          <td class="px-5 py-3.5"><span class="<?= $stBadge ?>"><?= esc($st ?: '-') ?></span></td>
          <td class="px-5 py-3.5">
            <a href="/ipsrs/aset/<?= esc($a['id'] ?? '') ?>/edit"
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium hover:underline">Edit</a>
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
            order: [[0, 'desc']]
        });
    }
});
</script>
