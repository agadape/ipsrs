<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs/aset"
     class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 hover:border-gray-200 transition-colors text-gray-500 hover:text-gray-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
  </a>
  <div>
    <h1 class="text-xl font-bold text-gray-800">Detail Katalog Aset</h1>
    <p class="text-sm font-medium text-indigo-600 mt-0.5"><?= esc($aset['nama'] ?? '') ?></p>
  </div>
</div>

<div class="card p-6 mb-6">
  <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    <div>
      <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Aset</p>
      <p class="text-sm font-semibold text-gray-800"><?= esc($aset['nama'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kategori</p>
      <p class="text-sm font-medium text-gray-700"><?= esc($aset['kategori'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Merk / Model</p>
      <p class="text-sm font-medium text-gray-700"><?= esc($aset['merk'] ?? '-') ?> / <?= esc($aset['model'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Jenis</p>
      <p class="text-sm font-medium text-gray-700"><?= esc($aset['jenis'] ?? '-') ?></p>
    </div>
  </div>
</div>

<div class="card p-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-gray-800">Daftar Series / Unit Fisik</h2>
    <a href="/ipsrs/aset/tambah-series/<?= esc($aset['id']) ?>" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
      + Tambah Series
    </a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead class="bg-gray-50/50">
        <tr>
          <th class="px-4 py-3 font-semibold text-gray-500 text-xs uppercase">No Aset</th>
          <th class="px-4 py-3 font-semibold text-gray-500 text-xs uppercase">No Seri</th>
          <th class="px-4 py-3 font-semibold text-gray-500 text-xs uppercase">Gedung / Unit</th>
          <th class="px-4 py-3 font-semibold text-gray-500 text-xs uppercase">Ruangan / Lantai</th>
          <th class="px-4 py-3 font-semibold text-gray-500 text-xs uppercase">Status</th>
          <th class="px-4 py-3 font-semibold text-gray-500 text-xs uppercase">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php foreach (($series ?? []) as $s): ?>
        <tr class="hover:bg-gray-50/50 transition-colors">
          <td class="px-4 py-3 font-bold text-indigo-600"><?= esc($s['nomor_aset']) ?></td>
          <td class="px-4 py-3 font-medium text-gray-600"><?= esc($s['no_seri'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-600"><?= esc($s['gedung']) ?> <br><span class="text-xs text-gray-400"><?= esc($s['unit']) ?></span></td>
          <td class="px-4 py-3 text-gray-600"><?= esc($s['ruangan']) ?> <br><span class="text-xs text-gray-400">Lt. <?= esc($s['lantai']) ?></span></td>
          <td class="px-4 py-3">
             <span class="px-2.5 py-1 rounded-md text-[11px] font-bold <?= ($s['status'] === 'Beroperasi' || $s['status'] === 'Aktif') ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' ?>">
               <?= esc($s['status'] ?? '-') ?>
             </span>
          </td>
          <td class="px-4 py-3">
            <a href="/ipsrs/aset/series/<?= esc($s['id']) ?>" class="text-indigo-600 hover:text-indigo-800 font-semibold text-xs bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">Lihat Detail</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($series)): ?>
        <tr><td colspan="6" class="text-center py-6 text-gray-400 text-sm">Belum ada series untuk aset ini.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>

