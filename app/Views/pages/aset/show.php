<div class="flex items-center gap-4 mb-8">
  <a href="/ipsrs/aset"
     class="w-10 h-10 flex items-center justify-center rounded-md bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700 shadow-sm">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
  </a>
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Katalog Aset</h1>
    <p class="text-sm font-medium text-slate-500 mt-1"><?= esc($aset['nama'] ?? '') ?></p>
  </div>
</div>

<div class="bg-white border border-slate-200 rounded-md shadow-sm p-6 mb-6">
  <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Aset</p>
      <p class="text-sm font-medium text-slate-900"><?= esc($aset['nama'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kategori</p>
      <p class="text-sm font-medium text-slate-900"><?= esc($aset['kategori'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Merk / Model</p>
      <p class="text-sm font-medium text-slate-900"><?= esc($aset['merk'] ?? '-') ?> / <?= esc($aset['model'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jenis</p>
      <p class="text-sm font-medium text-slate-900"><?= esc($aset['jenis'] ?? '-') ?></p>
    </div>
  </div>
</div>

<div class="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
  <div class="p-6 border-b border-slate-100 flex items-center justify-between">
    <h2 class="text-sm font-bold text-slate-800">Daftar Series / Unit Fisik</h2>
    <a href="/ipsrs/aset/tambah-series/<?= esc($aset['id']) ?>" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
      Tambah Series
    </a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">No Aset</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">No Seri</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Gedung / Unit</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Ruangan / Lantai</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach (($series ?? []) as $s): ?>
        <tr class="hover:bg-slate-50 transition-colors">
          <td class="px-6 py-4 font-mono font-medium text-indigo-600"><?= esc($s['nomor_aset']) ?></td>
          <td class="px-6 py-4 font-mono text-slate-600"><?= esc($s['no_seri'] ?? '-') ?></td>
          <td class="px-6 py-4 text-slate-600"><?= esc($s['gedung']) ?> <br><span class="text-xs text-slate-500"><?= esc($s['unit']) ?></span></td>
          <td class="px-6 py-4 text-slate-600"><?= esc($s['ruangan']) ?> <br><span class="text-xs text-slate-500">Lt. <?= esc($s['lantai']) ?></span></td>
          <td class="px-6 py-4">
             <span class="px-2.5 py-1 rounded border <?= ($s['status'] === 'Beroperasi' || $s['status'] === 'Aktif') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' ?> text-xs font-medium">
               <?= esc($s['status'] ?? '-') ?>
             </span>
          </td>
          <td class="px-6 py-4">
            <a href="/ipsrs/aset/series/<?= esc($s['id']) ?>" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs hover:underline transition-colors">Lihat Detail</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($series)): ?>
        <tr><td colspan="6" class="text-center py-8 text-slate-500 text-sm">Belum ada series untuk aset ini.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


