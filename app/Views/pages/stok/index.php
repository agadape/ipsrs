<?php
$filterParam = $filter ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-100">Stok &amp; Suku Cadang</h1>
    <p class="text-sm text-gray-400 mt-0.5">Kelola persediaan material dan suku cadang</p>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════
     ACTION HUB — Collapsible Forms for Better UX
     ════════════════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <!-- Trigger: Tambah Barang -->
  <button type="button" onclick="toggleStokForm('form-tambah')"
          class="group card p-5 flex flex-col items-center justify-center text-center hover:shadow-xl hover:-translate-y-1 hover:shadow-indigo-500/10 transition-all duration-300 border border-transparent hover:border-indigo-200">
    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300 shadow-inner">
      <svg class="w-6 h-6 text-[#CCFF00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
    </div>
    <h2 class="text-sm font-bold text-gray-100">Barang Baru</h2>
    <p class="text-[11px] text-gray-400 mt-1">Daftarkan item ke master data</p>
  </button>

  <!-- Trigger: Catat Masuk -->
  <button type="button" onclick="toggleStokForm('form-masuk')"
          class="group card p-5 flex flex-col items-center justify-center text-center hover:shadow-xl hover:-translate-y-1 hover:shadow-emerald-500/10 transition-all duration-300 border border-transparent hover:border-emerald-200">
    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300 shadow-inner">
      <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
    </div>
    <h2 class="text-sm font-bold text-gray-100">Stok Masuk</h2>
    <p class="text-[11px] text-gray-400 mt-1">Catat penerimaan barang</p>
  </button>

  <!-- Trigger: Catat Keluar -->
  <button type="button" onclick="toggleStokForm('form-keluar')"
          class="group card p-5 flex flex-col items-center justify-center text-center hover:shadow-xl hover:-translate-y-1 hover:shadow-red-500/10 transition-all duration-300 border border-transparent hover:border-red-200">
    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300 shadow-inner">
      <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8V4m0 0l4 4m-4-4l-4 4M7 16v4m0 0l-4-4m4 4l4-4"/></svg>
    </div>
    <h2 class="text-sm font-bold text-gray-100">Stok Keluar</h2>
    <p class="text-[11px] text-gray-400 mt-1">Catat pemakaian material</p>
  </button>
</div>

<!-- Forms Container -->
<div id="stok-forms-container">
  
  <!-- Form Tambah Barang -->
  <div id="form-tambah" class="stok-form hidden card p-6 md:p-8 mb-6 border-t-4 border-indigo-500 shadow-xl shadow-indigo-500/10">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-base font-bold text-gray-100">Daftarkan Barang Baru</h2>
      <button onclick="toggleStokForm('form-tambah')" class="text-gray-400 hover:text-gray-600 bg-[#202532] hover:bg-white/15 p-2 rounded-xl transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <form method="POST" action="/ipsrs/stok/tambah-barang" class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <?= csrf_field() ?>
      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Nama Barang <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required placeholder="Contoh: Lampu LED Philips 12W" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 shadow-inner">
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Kategori</label>
        <select name="kategori" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 appearance-none shadow-inner">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach (['Suku Cadang AC', 'Material Listrik', 'Perpipaan', 'Alat Ukur', 'Consumable', 'Lainnya'] as $opt): ?>
          <option value="<?= $opt ?>"><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Satuan</label>
        <select name="satuan" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 appearance-none shadow-inner">
          <option value="pcs">pcs</option><option value="unit">unit</option><option value="meter">meter</option>
          <option value="liter">liter</option><option value="kg">kg</option><option value="set">set</option><option value="roll">roll</option>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Stok Minimum (Peringatan)</label>
        <input type="number" name="minimum_stok" min="0" value="5" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 shadow-inner">
      </div>
      <div class="md:col-span-2 pt-2">
        <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 text-white text-[15px] font-bold rounded-2xl transition-all duration-300">
          Simpan Barang ke Master Data
        </button>
      </div>
    </form>
  </div>

  <!-- Form Catat Masuk -->
  <div id="form-masuk" class="stok-form hidden card p-6 md:p-8 mb-6 border-t-4 border-emerald-500 shadow-xl shadow-emerald-500/10">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-base font-bold text-gray-100">Catat Penerimaan Barang Masuk</h2>
      <button onclick="toggleStokForm('form-masuk')" class="text-gray-400 hover:text-gray-600 bg-[#202532] hover:bg-white/15 p-2 rounded-xl transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <form method="POST" action="/ipsrs/stok/masuk" class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <?= csrf_field() ?>
      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Pilih Barang <span class="text-red-500">*</span></label>
        <select name="id_barang" required class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/50 appearance-none shadow-inner">
          <option value="">-- Pilih Barang dari Master Data --</option>
          <?php foreach (($stok ?? []) as $s): ?>
          <option value="<?= esc($s['id'] ?? '') ?>"><?= esc($s['nama'] ?? '') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Jumlah <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah" min="1" required placeholder="0" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/50 shadow-inner">
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Tanggal Masuk</label>
        <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/50 shadow-inner">
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">No. Dokumen / PO</label>
        <input type="text" name="no_dokumen" placeholder="Contoh: INV-2023/001" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/50 shadow-inner">
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Keterangan Tambahan</label>
        <input type="text" name="keterangan" placeholder="Opsional" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/50 shadow-inner">
      </div>
      <div class="md:col-span-2 pt-2">
        <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5 text-white text-[15px] font-bold rounded-2xl transition-all duration-300">
          Simpan Transaksi Masuk
        </button>
      </div>
    </form>
  </div>

  <!-- Form Catat Keluar -->
  <div id="form-keluar" class="stok-form hidden card p-6 md:p-8 mb-6 border-t-4 border-red-500 shadow-xl shadow-red-500/10">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-base font-bold text-gray-100">Catat Pengeluaran Material</h2>
      <button onclick="toggleStokForm('form-keluar')" class="text-gray-400 hover:text-gray-600 bg-[#202532] hover:bg-white/15 p-2 rounded-xl transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <form method="POST" action="/ipsrs/stok/keluar" class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <?= csrf_field() ?>
      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Pilih Barang <span class="text-red-500">*</span></label>
        <select name="id_barang" required class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-red-500/50 appearance-none shadow-inner">
          <option value="">-- Pilih Barang dari Gudang --</option>
          <?php foreach (($stok ?? []) as $s): ?>
          <option value="<?= esc($s['id'] ?? '') ?>"><?= esc($s['nama'] ?? '') ?> (Tersedia: <?= (int)($s['stok_tersedia']??0) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Jumlah Dipakai <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah" min="1" required placeholder="0" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-red-500/50 shadow-inner">
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Tanggal Keluar</label>
        <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-red-500/50 shadow-inner">
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Keperluan / Laporan LK</label>
        <input type="text" name="no_dokumen" placeholder="Contoh: LK-230501" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-red-500/50 shadow-inner">
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Keterangan Teknisi</label>
        <input type="text" name="keterangan" placeholder="Opsional" class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-red-500/50 shadow-inner">
      </div>
      <div class="md:col-span-2 pt-2">
        <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-red-500 to-rose-600 hover:shadow-lg hover:shadow-red-500/30 hover:-translate-y-0.5 text-white text-[15px] font-bold rounded-2xl transition-all duration-300">
          Simpan Transaksi Keluar
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
<div class="flex flex-wrap gap-2 mb-5">
  <?php
  $tabs = ['' => 'Semua', 'Aman' => 'Aman', 'Menipis' => 'Menipis', 'Habis' => 'Habis'];
  foreach ($tabs as $val => $label):
    $active = $filterParam === $val;
  ?>
  <a href="/ipsrs/stok<?= $val ? '?status='.urlencode($val) : '' ?>"
     class="px-5 py-2.5 rounded-xl text-[13px] font-bold transition-all duration-300
       <?= $active ? 'bg-[#CCFF00] text-black border-none text-white shadow-md shadow-indigo-500/30' : 'bg-white/80 text-gray-400 hover:bg-white hover:text-gray-800 border border-white/10' ?>">
    <?= $label ?>
    <?php if ($val === 'Menipis'): ?>
    <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full <?= $active ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' ?>">
      <?= count(array_filter($stok ?? [], fn($s) => ($s['status'] ?? '') === 'Menipis')) ?>
    </span>
    <?php endif; ?>
    <?php if ($val === 'Habis'): ?>
    <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full <?= $active ? 'bg-white/20 text-white' : 'bg-red-100 text-red-600' ?>">
      <?= count(array_filter($stok ?? [], fn($s) => ($s['status'] ?? '') === 'Habis')) ?>
    </span>
    <?php endif; ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Stok Table -->
<div class="card overflow-hidden border border-white/50 shadow-lg shadow-indigo-500/5">
  <?php if (empty($stok)): ?>
  <div class="text-center py-20">
    <div class="w-16 h-16 rounded-full bg-[#202532] flex items-center justify-center mx-auto mb-4">
      <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    </div>
    <p class="text-sm text-gray-400 font-medium">Tidak ada data stok untuk filter ini.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-[14px]">
      <thead class="bg-gray-50/80 border-b border-gray-200/60">
        <tr>
          <th class="text-left px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Nama Barang</th>
          <th class="text-left px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Kategori</th>
          <th class="text-left px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Satuan</th>
          <th class="text-right px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Tersedia</th>
          <th class="text-right px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Min. Stok</th>
          <th class="text-left px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php foreach ($stok as $s): ?>
        <?php
          $st = $s['status'] ?? 'Aman';
          $stBadge = status_stok_badge($st);
          $stLabel = $st;
          $tersedia = (int)($s['stok_tersedia'] ?? 0);
          $minimum  = (int)($s['minimum_stok'] ?? 0);
          $numClass = $tersedia <= 0 ? 'font-bold text-red-600 bg-red-50 px-2 py-1 rounded-lg' : ($tersedia <= $minimum ? 'font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg' : 'font-bold text-gray-100');
        ?>
        <tr class="hover:bg-indigo-50/40 transition-colors group cursor-pointer">
          <td class="px-6 py-4 font-bold text-gray-100"><?= esc($s['nama'] ?? '-') ?></td>
          <td class="px-6 py-4 text-gray-300 font-medium"><?= esc($s['kategori'] ?? '-') ?></td>
          <td class="px-6 py-4 text-gray-400"><?= esc($s['satuan'] ?? '-') ?></td>
          <td class="px-6 py-4 text-right"><span class="<?= $numClass ?> inline-block min-w-[3rem] text-center"><?= $tersedia ?></span></td>
          <td class="px-6 py-4 text-right text-gray-400 font-mono"><?= $minimum ?></td>
          <td class="px-6 py-4"><span class="<?= $stBadge ?> shadow-[0_4px_20px_rgba(0,0,0,0.5)]"><?= $stLabel ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
