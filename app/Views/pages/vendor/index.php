<?php $total = count($vendor ?? []); ?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-800">Data Vendor</h1>
    <p class="text-sm text-gray-400 mt-0.5">Master data vendor / pihak ke-3 untuk perbaikan Proses III</p>
  </div>
</div>

<!-- Tambah Vendor -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-5">
    <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center">
      <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
      </svg>
    </div>
    <h2 class="text-sm font-semibold text-gray-700">Tambah Vendor</h2>
  </div>
  <form method="POST" action="/ipsrs/vendor/tambah">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Vendor <span class="text-red-500">*</span></label>
        <input type="text" name="nama_vendor" value="<?= esc(old('nama_vendor') ?? '') ?>" required
               placeholder="Nama perusahaan vendor"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak</label>
        <input type="text" name="kontak" value="<?= esc(old('kontak') ?? '') ?>"
               placeholder="No. telp / email"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
        <input type="text" name="alamat" value="<?= esc(old('alamat') ?? '') ?>"
               placeholder="Alamat vendor"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
      </div>
    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit"
              class="px-8 py-3 bg-gradient-to-r from-teal-500 to-emerald-500 hover:shadow-lg hover:shadow-teal-500/30 hover:-translate-y-0.5 text-white text-[14px] font-bold rounded-2xl transition-all duration-300">
        Simpan Vendor
      </button>
    </div>
  </form>
</div>

<!-- Search -->
<div class="card p-4 mb-5">
  <form method="GET" action="/ipsrs/vendor" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari Vendor</label>
      <input type="text" name="q" value="<?= esc($search ?? '') ?>"
             placeholder="Nama vendor..."
             class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
    </div>
    <button type="submit" class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all duration-300">Cari</button>
    <?php if (!empty($search)): ?>
    <a href="/ipsrs/vendor" class="px-5 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors">Reset</a>
    <?php endif; ?>
  </form>
</div>

<p class="text-sm text-gray-500 mb-3">Menampilkan <span class="font-semibold text-gray-700"><?= $total ?></span> vendor</p>

<!-- Table -->
<div class="card overflow-hidden">
  <?php if (empty($vendor)): ?>
  <div class="text-center py-16">
    <p class="text-sm text-gray-400">Belum ada data vendor.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50/80 border-b border-gray-200/60">
        <tr>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Nama Vendor</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Kontak</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Alamat</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($vendor as $v): ?>
        <tr class="hover:bg-teal-50/40 transition-colors group">
          <td class="px-5 py-3.5 font-medium text-gray-800"><?= esc($v['nama_vendor'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($v['kontak'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-500 max-w-[260px] truncate"><?= esc($v['alamat'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-right">
            <button type="button"
                    onclick="editVendor('<?= esc($v['id'] ?? '') ?>', this)"
                    data-nama="<?= esc($v['nama_vendor'] ?? '') ?>"
                    data-kontak="<?= esc($v['kontak'] ?? '') ?>"
                    data-alamat="<?= esc($v['alamat'] ?? '') ?>"
                    class="text-xs text-teal-600 hover:text-teal-800 font-medium hover:underline">Edit</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="absolute inset-0 bg-black/50" onclick="closeEdit()"></div>
  <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
    <h3 class="text-sm font-semibold text-gray-800 mb-4">Edit Vendor</h3>
    <form id="edit-form" method="POST">
      <?= csrf_field() ?>
      <div class="space-y-3">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Vendor <span class="text-red-500">*</span></label>
          <input type="text" name="nama_vendor" id="edit-nama" required
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak</label>
          <input type="text" name="kontak" id="edit-kontak"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
          <input type="text" name="alamat" id="edit-alamat"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
        </div>
      </div>
      <div class="mt-5 flex items-center justify-end gap-3">
        <button type="button" onclick="closeEdit()" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Batal</button>
        <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl transition-colors">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
  function editVendor(id, btn) {
    document.getElementById('edit-form').action = '/ipsrs/vendor/' + id + '/edit';
    document.getElementById('edit-nama').value   = btn.dataset.nama || '';
    document.getElementById('edit-kontak').value = btn.dataset.kontak || '';
    document.getElementById('edit-alamat').value = btn.dataset.alamat || '';
    document.getElementById('edit-modal').classList.remove('hidden');
  }
  function closeEdit() {
    document.getElementById('edit-modal').classList.add('hidden');
  }
</script>
