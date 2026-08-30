<?php
$total = count($aset ?? []);
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Aset</h1>
    <p class="text-sm text-slate-500 mt-1">Kelola inventaris aset rumah sakit</p>
  </div>
  <a href="/ipsrs/aset/tambah"
     class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
    </svg>
    Tambah Aset
  </a>
</div>

<!-- Filter Bar -->
<div class="card p-4 mb-6">
  <form method="GET" action="/ipsrs/aset" class="flex flex-wrap gap-4 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Cari Aset</label>
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35" stroke-linecap="round" stroke-width="2"/>
        </svg>
        <input type="text" name="q" value="<?= esc($search ?? '') ?>"
               placeholder="Nama, ID, kode aset..."
               class="w-full pl-9 pr-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 focus:border-red-600 transition-colors shadow-sm">
      </div>
    </div>
    <div class="min-w-[160px]">
      <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Jenis</label>
      <select name="jenis" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 focus:border-red-600 transition-colors shadow-sm">
        <option value="">Semua Jenis</option>
        <option value="Sarana"      <?= ($jenis ?? '') === 'Sarana'      ? 'selected' : '' ?>>Sarana</option>
        <option value="Prasarana"   <?= ($jenis ?? '') === 'Prasarana'   ? 'selected' : '' ?>>Prasarana</option>
        <option value="Alat Non Medis" <?= ($jenis ?? '') === 'Alat Non Medis' ? 'selected' : '' ?>>Alat Non Medis</option>
      </select>
    </div>
    <div class="min-w-[160px]">
      <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Status</label>
      <select name="status" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 focus:border-red-600 transition-colors shadow-sm">
        <option value="">Semua Status</option>
        <option value="Tersedia" <?= ($status ?? '') === 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
        <option value="Dipinjam" <?= ($status ?? '') === 'Dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
        <option value="Dalam Perbaikan" <?= ($status ?? '') === 'Dalam Perbaikan' ? 'selected' : '' ?>>Dalam Perbaikan</option>
        <option value="Rusak Berat" <?= ($status ?? '') === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat (Siap Kanibal/Afkir)</option>
        <option value="Dihapuskan" <?= ($status ?? '') === 'Dihapuskan' ? 'selected' : '' ?>>Dihapuskan</option>
      </select>
    </div>
    <div>
      <button type="submit" class="h-[38px] px-4 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
        Filter
      </button>
    </div>
  </form>
  
  <!-- Quick Tabs -->
  <div class="flex gap-2 mt-4 pt-4 border-t border-slate-100 overflow-x-auto">
    <a href="/ipsrs/aset" class="px-3 py-1.5 text-sm font-medium rounded-md <?= empty($status) ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:bg-slate-50' ?>">Semua Aset</a>
    <a href="/ipsrs/aset?status=Tersedia" class="px-3 py-1.5 text-sm font-medium rounded-md <?= ($status ?? '') === 'Tersedia' ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:bg-slate-50' ?>">Tersedia</a>
    <a href="/ipsrs/aset?status=Dipinjam" class="px-3 py-1.5 text-sm font-medium rounded-md <?= ($status ?? '') === 'Dipinjam' ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:bg-slate-50' ?>">Sedang Dipinjam</a>
    <a href="/ipsrs/aset?status=Rusak+Berat" class="px-3 py-1.5 text-sm font-medium rounded-md <?= ($status ?? '') === 'Rusak Berat' ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:bg-slate-50' ?>">Siap Kanibal / Afkir</a>
    <a href="/ipsrs/aset?status=Dihapuskan" class="px-3 py-1.5 text-sm font-medium rounded-md <?= ($status ?? '') === 'Dihapuskan' ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:bg-slate-50' ?>">Riwayat Dihapuskan</a>
  </div>
</div>

<!-- Count -->
<p class="text-sm text-slate-500 mb-4">Menampilkan <span class="font-semibold text-slate-900"><?= $total ?></span> aset</p>

<!-- Table -->
<div class="card overflow-hidden">
  <?php if (empty($aset)): ?>
  <div class="p-12 text-center flex flex-col items-center justify-center">
    <div class="w-16 h-16 bg-slate-50 border border-slate-200 rounded-md flex items-center justify-center mb-4">
      <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    </div>
    <h3 class="text-sm font-bold text-slate-800 mb-1">Belum Ada Aset</h3>
    <p class="text-sm text-slate-500 max-w-sm mx-auto mb-6">Data aset saat ini kosong atau tidak ditemukan.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table id="tabel-data" class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">ID Aset</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Master Aset</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jenis</th>
          <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Unit</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Unit</th>
          <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($aset as $a): ?>
        <?php
          $jn = $a['jenis'] ?? '';
          $jnBadge = match($jn) {
            'Sarana'         => 'px-2.5 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded-md border border-slate-200/60',
            'Prasarana'      => 'px-2.5 py-1 text-xs font-medium bg-red-50 text-red-800 rounded-md border border-red-200/60',
            'Alat Non Medis' => 'px-2.5 py-1 text-xs font-medium bg-amber-50 text-amber-700 rounded-md border border-amber-200/60',
            default          => 'px-2.5 py-1 text-xs font-medium bg-slate-50 text-slate-600 rounded-md border border-slate-200',
          };
          $unitCount = (int)($a['total_series'] ?? 0);
          $statusList = explode(',', $a['all_statuses'] ?? '');
          $statusCounts = array_count_values(array_filter($statusList));
        ?>
        <tr class="hover:bg-slate-50/80 transition-colors">
          <td class="px-4 py-3.5">
            <span class="font-mono text-xs text-slate-500 font-medium"><?= esc($a['nomor_aset'] ?? substr($a['id'] ?? '', 0, 8)) ?></span>
          </td>
          <td class="px-4 py-3.5 align-middle">
            <a href="/ipsrs/aset/<?= esc($a['id'] ?? '') ?>" class="font-semibold text-slate-900 hover:text-red-700 transition-colors text-sm">
              <?= esc($a['nama'] ?? '-') ?>
            </a>
            <?php if (!empty($a['keterangan'])): ?>
            <p class="text-xs text-slate-400 mt-0.5 line-clamp-1 max-w-xs">
              <?= esc($a['keterangan']) ?>
            </p>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3.5 text-slate-600 font-medium text-xs"><?= esc($a['kategori'] ?? '-') ?></td>
          <td class="px-4 py-3.5"><span class="<?= $jnBadge ?>"><?= esc($jn ?: '-') ?></span></td>
          <td class="px-4 py-3.5 text-center">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono font-semibold bg-slate-100 text-slate-700 border border-slate-200/70">
              <?= $unitCount ?> unit
            </span>
          </td>
          <td class="px-4 py-3.5">
            <div class="flex flex-wrap gap-1">
              <?php foreach($statusCounts as $st => $count): 
                $color = match($st) {
                  "Tersedia" => "bg-blue-50 text-blue-700 border-blue-200",
                  "Dipinjam" => "bg-emerald-50 text-emerald-700 border-emerald-200",
                  "Dalam Perbaikan" => "bg-yellow-50 text-yellow-700 border-yellow-200",
                  "Rusak Berat" => "bg-red-50 text-red-700 border-red-200",
                  "Dihapuskan" => "bg-slate-100 text-slate-600 border-slate-200",
                  default => "bg-slate-50 text-slate-600 border-slate-200"
                };
              ?>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium border <?= $color ?>">
                  <?= $count ?> <?= esc($st) ?>
                </span>
              <?php endforeach; ?>
              <?php if (empty($statusCounts)): ?>
                <span class="text-xs text-slate-400 italic">Belum ada unit</span>
              <?php endif; ?>
            </div>
          </td>
          <td class="px-4 py-3.5 text-right whitespace-nowrap">
            <div class="inline-flex items-center gap-2">
              <a href="/ipsrs/aset/<?= esc($a['id'] ?? '') ?>"
                 class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50/70 hover:bg-red-100 rounded-md border border-red-200/50 transition-colors">
                Lihat Unit
              </a>
              <a href="/ipsrs/aset/<?= esc($a['id'] ?? '') ?>/edit"
                 class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-600 bg-white hover:bg-slate-50 rounded-md border border-slate-200 shadow-sm transition-colors">
                Edit
              </a>
            </div>
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



