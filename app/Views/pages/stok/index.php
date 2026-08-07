<?php
$filterParam = $filter ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Stok &amp; Suku Cadang</h1>
    <p class="text-sm text-slate-500 mt-1">Kelola persediaan material dan suku cadang</p>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════
     ACTION HUB — Collapsible Forms for Better UX
     ════════════════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <!-- Trigger: Tambah Barang -->
  <button type="button" onclick="toggleStokForm('form-tambah')"
          class="card p-5 flex flex-col items-center justify-center text-center hover:bg-slate-50 transition-colors border border-slate-200">
    <div class="w-10 h-10 rounded-md bg-red-50 flex items-center justify-center mb-3">
      <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
    </div>
    <h2 class="text-sm font-semibold text-slate-800">Barang Baru</h2>
    <p class="text-xs text-slate-500 mt-1">Daftarkan item ke master data</p>
  </button>

  <!-- Trigger: Catat Masuk -->
  <button type="button" onclick="toggleStokForm('form-masuk')"
          class="card p-5 flex flex-col items-center justify-center text-center hover:bg-slate-50 transition-colors border border-slate-200">
    <div class="w-10 h-10 rounded-md bg-emerald-50 flex items-center justify-center mb-3">
      <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
    </div>
    <h2 class="text-sm font-semibold text-slate-800">Stok Masuk</h2>
    <p class="text-xs text-slate-500 mt-1">Catat penerimaan barang</p>
  </button>

  <!-- Trigger: Catat Keluar -->
  <button type="button" onclick="toggleStokForm('form-keluar')"
          class="card p-5 flex flex-col items-center justify-center text-center hover:bg-slate-50 transition-colors border border-slate-200">
    <div class="w-10 h-10 rounded-md bg-red-50 flex items-center justify-center mb-3">
      <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8V4m0 0l4 4m-4-4l-4 4M7 16v4m0 0l-4-4m4 4l4-4"/></svg>
    </div>
    <h2 class="text-sm font-semibold text-slate-800">Stok Keluar</h2>
    <p class="text-xs text-slate-500 mt-1">Catat pemakaian material</p>
  </button>
</div>

<!-- Forms Container -->
<div id="stok-forms-container">
  
  <!-- Form Tambah Barang -->
  <div id="form-tambah" class="stok-form hidden card p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-base font-bold text-slate-800">Daftarkan Barang Baru</h2>
      <button onclick="toggleStokForm('form-tambah')" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 p-2 rounded-md transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <form method="POST" action="/ipsrs/stok/tambah-barang" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?= csrf_field() ?>
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Barang <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required placeholder="Contoh: Lampu LED Philips 12W" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kategori</label>
        <select name="kategori" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach (['Suku Cadang AC', 'Material Listrik', 'Perpipaan', 'Alat Ukur', 'Consumable', 'Lainnya'] as $opt): ?>
          <option value="<?= $opt ?>"><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Satuan</label>
        <select name="satuan" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
          <option value="pcs">pcs</option><option value="unit">unit</option><option value="meter">meter</option>
          <option value="liter">liter</option><option value="kg">kg</option><option value="set">set</option><option value="roll">roll</option>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Stok Minimum (Peringatan)</label>
        <input type="number" name="minimum_stok" min="0" value="5" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div class="md:col-span-2 pt-2 flex justify-end">
        <button type="submit" class="px-6 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
          Simpan Barang
        </button>
      </div>
    </form>
  </div>

  <!-- Form Catat Masuk -->
  <div id="form-masuk" class="stok-form hidden card p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-base font-bold text-slate-800">Catat Penerimaan Barang Masuk</h2>
      <button onclick="toggleStokForm('form-masuk')" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 p-2 rounded-md transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <form method="POST" action="/ipsrs/stok/masuk" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?= csrf_field() ?>
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Pilih Barang <span class="text-red-500">*</span></label>
        <select name="id_barang" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
          <option value="">-- Pilih Barang dari Master Data --</option>
          <?php foreach (($stok ?? []) as $s): ?>
          <option value="<?= esc($s['id'] ?? '') ?>"><?= esc($s['nama'] ?? '') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Jumlah <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah" min="1" required placeholder="0" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal Masuk</label>
        <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">No. Dokumen / PO</label>
        <input type="text" name="no_dokumen" placeholder="Contoh: INV-2023/001" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Keterangan Tambahan</label>
        <input type="text" name="keterangan" placeholder="Opsional" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div class="md:col-span-2 pt-2 flex justify-end">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
          Simpan Transaksi
        </button>
      </div>
    </form>
  </div>

  <!-- Form Catat Keluar -->
  <div id="form-keluar" class="stok-form hidden card p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-base font-bold text-slate-800">Catat Pengeluaran Material</h2>
      <button onclick="toggleStokForm('form-keluar')" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 p-2 rounded-md transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <form method="POST" action="/ipsrs/stok/keluar" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?= csrf_field() ?>
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Pilih Barang <span class="text-red-500">*</span></label>
        <select name="id_barang" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
          <option value="">-- Pilih Barang dari Gudang --</option>
          <?php foreach (($stok ?? []) as $s): ?>
          <option value="<?= esc($s['id'] ?? '') ?>"><?= esc($s['nama'] ?? '') ?> (Tersedia: <?= (int)($s['stok_tersedia']??0) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Jumlah Dipakai <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah" min="1" required placeholder="0" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal Keluar</label>
        <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Keperluan / Laporan LK</label>
        <input type="text" name="no_dokumen" placeholder="Contoh: LK-230501" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Keterangan Teknisi</label>
        <input type="text" name="keterangan" placeholder="Opsional" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div class="md:col-span-2 pt-2 flex justify-end">
        <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
          Simpan Transaksi
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleStokForm(formId) {
  const forms = document.querySelectorAll('.stok-form');
  forms.forEach(f => {
    if (f.id === formId) {
      if (f.classList.contains('hidden')) {
        f.classList.remove('hidden');
        f.scrollIntoView({ behavior: 'smooth', block: 'center' });
      } else {
        f.classList.add('hidden');
      }
    } else {
      f.classList.add('hidden');
    }
  });
}
</script>

<!-- Filter Tabs -->
<div class="flex flex-wrap gap-2 mb-4">
  <?php
  $tabs = ['' => 'Semua', 'Aman' => 'Aman', 'Menipis' => 'Menipis', 'Habis' => 'Habis'];
  foreach ($tabs as $val => $label):
    $active = $filterParam === $val;
  ?>
  <a href="/ipsrs/stok<?= $val ? '?status='.urlencode($val) : '' ?>"
     class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors border
       <?= $active ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900' ?>">
    <?= $label ?>
    <?php if ($val === 'Menipis'): ?>
    <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full <?= $active ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-600 border border-amber-200' ?>">
      <?= count(array_filter($stok ?? [], fn($s) => ($s['status'] ?? '') === 'Menipis')) ?>
    </span>
    <?php endif; ?>
    <?php if ($val === 'Habis'): ?>
    <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full <?= $active ? 'bg-white/20 text-white' : 'bg-red-50 text-red-600 border border-red-200' ?>">
      <?= count(array_filter($stok ?? [], fn($s) => ($s['status'] ?? '') === 'Habis')) ?>
    </span>
    <?php endif; ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Stok Table -->
<div class="card overflow-hidden">
  <?php if (empty($stok)): ?>
  <div class="text-center py-16">
    <p class="text-sm text-slate-500 font-medium">Tidak ada data stok untuk filter ini.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Barang</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Kategori</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Satuan</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider text-right">Tersedia</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider text-right">Min. Stok</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($stok as $s): ?>
        <?php
          $st = $s['status'] ?? 'Aman';
          $stBadge = status_stok_badge($st);
          $stLabel = $st;
          $tersedia = (int)($s['stok_tersedia'] ?? 0);
          $minimum  = (int)($s['minimum_stok'] ?? 0);
          $numClass = $tersedia <= 0 ? 'font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded' : ($tersedia <= $minimum ? 'font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded' : 'font-medium text-slate-900');
        ?>
        <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="window.location.href='/ipsrs/stok/riwayat?id=<?= esc($s['id']) ?>'">
          <td class="px-4 py-3 font-medium text-slate-900 group-hover:text-red-700 transition-colors"><?= esc($s['nama'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($s['kategori'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-500"><?= esc($s['satuan'] ?? '-') ?></td>
          <td class="px-4 py-3 text-right"><span class="<?= $numClass ?> inline-block min-w-[2.5rem] text-center"><?= $tersedia ?></span></td>
          <td class="px-4 py-3 text-right text-slate-400 font-mono"><?= $minimum ?></td>
          <td class="px-4 py-3"><span class="<?= $stBadge ?> shadow-sm"><?= $stLabel ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
