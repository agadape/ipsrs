<?php
$total = count($aset ?? []);
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-100">Daftar Aset</h1>
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
      <label class="block text-xs font-medium text-gray-400 mb-1.5">Cari Aset</label>
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35" stroke-linecap="round"/>
        </svg>
        <input type="text" name="q" value="<?= esc($search ?? '') ?>"
               placeholder="Nama, ID, kode aset..."
               class="w-full pl-9 pr-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>
    </div>
    <div class="min-w-[160px]">
      <label class="block text-xs font-medium text-gray-400 mb-1.5">Jenis</label>
      <select name="jenis" class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
        <option value="">Semua Jenis</option>
        <option value="Sarana"      <?= ($jenis ?? '') === 'Sarana'      ? 'selected' : '' ?>>Sarana</option>
        <option value="Prasarana"   <?= ($jenis ?? '') === 'Prasarana'   ? 'selected' : '' ?>>Prasarana</option>
        <option value="Alat Non Medis" <?= ($jenis ?? '') === 'Alat Non Medis' ? 'selected' : '' ?>>Alat Non Medis</option>
      </select>
    </div>
    <div class="min-w-[160px]">
      <label class="block text-xs font-medium text-gray-400 mb-1.5">Status</label>
      <select name="status" class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
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
    <a href="/ipsrs/aset" class="px-4 py-2.5 text-sm text-gray-400 hover:text-gray-700 rounded-xl bg-[#202532] hover:bg-white/15 transition-colors">Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- Count -->
<p class="text-sm text-gray-400 mb-3">Menampilkan <span class="font-semibold text-gray-200"><?= $total ?></span> aset</p>

<!-- Table -->
<div class="card overflow-hidden">
  <?php if (empty($aset)): ?>
  <div class="text-center py-16">
    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
    </svg>
    <p class="text-sm text-gray-400">Tidak ada aset ditemukan.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-[#181C25]/80 border-b border-white/5">
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
            'Sarana'         => 'badge bg-[#CCFF00]/10 text-indigo-700',
            'Prasarana'      => 'badge bg-slate-100 text-slate-600',
            'Alat Non Medis' => 'badge bg-[#202532] text-gray-300',
            default          => 'badge bg-[#202532] text-gray-400',
          };
        ?>
        <tr class="hover:bg-indigo-50/40 transition-colors group">
          <td class="px-5 py-3.5">
            <span class="font-mono text-xs text-[#CCFF00] font-semibold"><?= esc($a['nomor_aset'] ?? $a['id'] ?? '-') ?></span>
          </td>
          <td class="px-5 py-3.5">
            <a href="/ipsrs/aset/<?= esc($a['id'] ?? '') ?>" class="font-medium text-gray-100 hover:text-indigo-600 transition-colors">
              <?= esc($a['nama'] ?? '-') ?>
            </a>
          </td>
          <td class="px-5 py-3.5 text-gray-300"><?= esc($a['kategori'] ?? '-') ?></td>
          <td class="px-5 py-3.5"><span class="<?= $jnBadge ?>"><?= esc($jn ?: '-') ?></span></td>
          <td class="px-5 py-3.5 text-gray-300"><?= esc($a['lokasi'] ?? '-') ?></td>
          <td class="px-5 py-3.5"><span class="<?= $stBadge ?>"><?= esc($st ?: '-') ?></span></td>
          <td class="px-5 py-3.5">
            <a href="/ipsrs/aset/<?= esc($a['id'] ?? '') ?>/edit"
               class="text-xs text-[#CCFF00] hover:text-indigo-800 font-medium hover:underline">Edit</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
