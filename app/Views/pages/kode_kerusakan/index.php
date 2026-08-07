<?php $total = count($kodeKerusakan ?? []); ?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kode Kerusakan</h1>
    <p class="text-sm text-slate-500 mt-1">Master data kode pekerjaan untuk Laporan Kerusakan</p>
  </div>
</div>

<!-- Tambah Kode -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-5">
    <h2 class="text-sm font-semibold text-slate-800">Tambah Kode Kerusakan</h2>
  </div>
  <form method="POST" action="/ipsrs/kode-kerusakan/tambah">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kode <span class="text-red-500">*</span></label>
        <input type="text" name="kode" value="<?= esc(old('kode') ?? '') ?>" required
               placeholder="Contoh: AC, PR, NM"
               maxlength="10"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors uppercase">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama / Keterangan <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="<?= esc(old('nama') ?? '') ?>" required
               placeholder="Contoh: Air Conditioning"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit"
              class="px-6 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
        Simpan Kode
      </button>
    </div>
  </form>
</div>

<!-- Search -->
<div class="card p-4 mb-6">
  <form method="GET" action="/ipsrs/kode-kerusakan" class="flex flex-wrap gap-4 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Cari Kode</label>
      <input type="text" name="q" value="<?= esc($search ?? '') ?>"
             placeholder="Kode atau nama..."
             class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
    </div>
    <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-md shadow-sm transition-colors">Cari</button>
    <?php if (!empty($search)): ?>
    <a href="/ipsrs/kode-kerusakan" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">Reset</a>
    <?php endif; ?>
  </form>
</div>

<p class="text-sm text-slate-500 mb-4">Menampilkan <span class="font-semibold text-slate-900"><?= $total ?></span> kode kerusakan</p>

<!-- Table -->
<div class="card overflow-hidden">
  <?php if (empty($kodeKerusakan)): ?>
  <div class="text-center py-16">
    <p class="text-sm text-slate-500">Belum ada data kode kerusakan.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left text-sm border-collapse">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Kode</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Nama / Keterangan</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider text-right"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($kodeKerusakan as $kk): ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3">
            <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 font-mono border border-slate-200"><?= esc($kk['kode'] ?? '-') ?></span>
          </td>
          <td class="px-4 py-3 font-medium text-slate-900"><?= esc($kk['nama'] ?? '-') ?></td>
          <td class="px-4 py-3 text-right">
            <button type="button"
                    onclick="editKode(this)"
                    data-id="<?= esc($kk['id'] ?? '') ?>"
                    data-kode="<?= esc($kk['kode'] ?? '') ?>"
                    data-nama="<?= esc($kk['nama'] ?? '') ?>"
                    class="text-xs text-red-700 hover:text-red-900 font-medium px-3 py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 transition-colors">Edit</button>
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
    <h3 class="text-base font-bold text-slate-800 mb-4">Edit Kode Kerusakan</h3>
    <form id="edit-form" method="POST">
      <?= csrf_field() ?>
      <div class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kode <span class="text-red-500">*</span></label>
          <input type="text" name="kode" id="edit-kode" required maxlength="10"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors uppercase">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama / Keterangan <span class="text-red-500">*</span></label>
          <input type="text" name="nama" id="edit-nama" required
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
        </div>
      </div>
      <div class="mt-6 flex items-center justify-between">
        <button type="button" onclick="hapusKode()" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors">Hapus</button>
        <div class="flex items-center gap-3">
          <button type="button" onclick="closeEdit()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Batal</button>
          <button type="submit" class="px-5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-medium rounded-md shadow-sm transition-colors">Simpan</button>
        </div>
      </div>
    </form>
    <form id="delete-form" method="POST" class="hidden">
      <?= csrf_field() ?>
    </form>
  </div>
</div>

<script>
  function editKode(btn) {
    document.getElementById('edit-form').action = '/ipsrs/kode-kerusakan/' + btn.dataset.id + '/edit';
    document.getElementById('edit-kode').value  = btn.dataset.kode || '';
    document.getElementById('edit-nama').value   = btn.dataset.nama || '';
    document.getElementById('edit-modal').classList.remove('hidden');
  }
  function closeEdit() {
    document.getElementById('edit-modal').classList.add('hidden');
  }
  function hapusKode() {
    if (confirm('Apakah Anda yakin ingin menghapus kode kerusakan ini?')) {
      const actionUrl = document.getElementById('edit-form').action.replace('/edit', '/delete');
      document.getElementById('delete-form').action = actionUrl;
      document.getElementById('delete-form').submit();
    }
  }
</script>
