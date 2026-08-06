<?php $total = count($vendor ?? []); ?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Vendor</h1>
    <p class="text-sm text-slate-500 mt-1">Master data vendor / pihak ke-3 untuk perbaikan Proses III</p>
  </div>
</div>

<!-- Tambah Vendor -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-5">
    <h2 class="text-sm font-semibold text-slate-800">Tambah Vendor</h2>
  </div>
  <form method="POST" action="/ipsrs/vendor/tambah">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Vendor <span class="text-red-500">*</span></label>
        <input type="text" name="nama_vendor" value="<?= esc(old('nama_vendor') ?? '') ?>" required
               placeholder="Nama perusahaan vendor"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kontak</label>
        <input type="text" name="kontak" value="<?= esc(old('kontak') ?? '') ?>"
               placeholder="No. telp / email"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Alamat</label>
        <input type="text" name="alamat" value="<?= esc(old('alamat') ?? '') ?>"
               placeholder="Alamat vendor"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>
    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit"
              class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
        Simpan Vendor
      </button>
    </div>
  </form>
</div>

<!-- Search -->
<div class="card p-4 mb-6">
  <form method="GET" action="/ipsrs/vendor" class="flex flex-wrap gap-4 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Cari Vendor</label>
      <input type="text" name="q" value="<?= esc($search ?? '') ?>"
             placeholder="Nama vendor..."
             class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
    </div>
    <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-md shadow-sm transition-colors">Cari</button>
    <?php if (!empty($search)): ?>
    <a href="/ipsrs/vendor" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">Reset</a>
    <?php endif; ?>
  </form>
</div>

<p class="text-sm text-slate-500 mb-4">Menampilkan <span class="font-semibold text-slate-900"><?= $total ?></span> vendor</p>

<!-- Table -->
<div class="card overflow-hidden">
  <?php if (empty($vendor)): ?>
  <div class="text-center py-16">
    <p class="text-sm text-slate-500">Belum ada data vendor.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left text-sm border-collapse">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Vendor</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Kontak</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Alamat</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider text-right"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($vendor as $v): ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 font-medium text-slate-900"><?= esc($v['nama_vendor'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($v['kontak'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-500 max-w-[260px] truncate"><?= esc($v['alamat'] ?? '-') ?></td>
          <td class="px-4 py-3 text-right">
            <button type="button"
                    onclick="editVendor('<?= esc($v['id'] ?? '') ?>', this)"
                    data-nama="<?= esc($v['nama_vendor'] ?? '') ?>"
                    data-kontak="<?= esc($v['kontak'] ?? '') ?>"
                    data-alamat="<?= esc($v['alamat'] ?? '') ?>"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium px-3 py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 transition-colors">Edit</button>
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
  <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeEdit()"></div>
  <div class="relative bg-white rounded-lg shadow-xl border border-slate-200 w-full max-w-md p-6">
    <h3 class="text-base font-bold text-slate-800 mb-4">Edit Vendor</h3>
    <form id="edit-form" method="POST">
      <?= csrf_field() ?>
      <div class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Vendor <span class="text-red-500">*</span></label>
          <input type="text" name="nama_vendor" id="edit-nama" required
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kontak</label>
          <input type="text" name="kontak" id="edit-kontak"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Alamat</label>
          <input type="text" name="alamat" id="edit-alamat"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
        </div>
      </div>
      <div class="mt-6 flex items-center justify-end gap-3">
        <button type="button" onclick="closeEdit()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Batal</button>
        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">Simpan</button>
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
