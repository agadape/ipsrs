<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs/aset/<?= esc($aset['id']) ?>"
     class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 hover:border-gray-200 transition-colors text-gray-500 hover:text-gray-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <div>
    <h1 class="text-xl font-bold text-gray-800"><?= $isEdit ? 'Edit Series' : 'Tambah Series Baru' ?></h1>
    <p class="text-sm font-medium text-indigo-600 mt-0.5">Katalog: <?= esc($aset['nama'] ?? '') ?></p>
  </div>
</div>

<div class="card p-6">
  <form method="POST" action="<?= $isEdit ? '/ipsrs/aset/series/' . esc($series['id']) . '/edit' : '/ipsrs/aset/tambah-series/' . esc($aset['id']) ?>">
    <?= csrf_field() ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-2">Nomor Aset / Inventaris <span class="text-red-500">*</span></label>
        <input type="text" name="nomor_aset" value="<?= esc(old('nomor_aset') ?? $series['nomor_aset'] ?? '') ?>" required
               class="w-full px-4 py-2.5 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-2">Nomor Seri (S/N)</label>
        <input type="text" name="no_seri" value="<?= esc(old('no_seri') ?? $series['no_seri'] ?? '') ?>"
               class="w-full px-4 py-2.5 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all">
      </div>
    </div>

    <div class="mb-6">
      <h3 class="text-sm font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Lokasi Penempatan</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-gray-600 mb-2">Lokasi Utama <span class="text-red-500">*</span></label>
          <input type="text" name="lokasi" value="<?= esc(old('lokasi') ?? $series['lokasi'] ?? '') ?>" required
                 class="w-full px-4 py-2.5 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-2">Gedung <span class="text-red-500">*</span></label>
          <input type="text" name="gedung" value="<?= esc(old('gedung') ?? $series['gedung'] ?? '') ?>" required
                 class="w-full px-4 py-2.5 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-2">Lantai</label>
          <input type="text" name="lantai" value="<?= esc(old('lantai') ?? $series['lantai'] ?? '') ?>"
                 class="w-full px-4 py-2.5 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-2">Ruangan <span class="text-red-500">*</span></label>
          <input type="text" name="ruangan" value="<?= esc(old('ruangan') ?? $series['ruangan'] ?? '') ?>" required
                 class="w-full px-4 py-2.5 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-2">Unit <span class="text-red-500">*</span></label>
          <input type="text" name="unit" value="<?= esc(old('unit') ?? $series['unit'] ?? '') ?>" required
                 class="w-full px-4 py-2.5 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all">
        </div>
      </div>
    </div>

    <div class="mb-6">
      <h3 class="text-sm font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Status & Fisik</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-2">Kondisi</label>
          <select name="kondisi" class="w-full px-4 py-2.5 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:bg-white appearance-none">
            <option value="Baik" <?= (old('kondisi') ?? $series['kondisi'] ?? '') === 'Baik' ? 'selected' : '' ?>>Baik</option>
            <option value="Rusak Ringan" <?= (old('kondisi') ?? $series['kondisi'] ?? '') === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
            <option value="Rusak Berat" <?= (old('kondisi') ?? $series['kondisi'] ?? '') === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-2">Status</label>
          <select name="status" class="w-full px-4 py-2.5 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:bg-white appearance-none">
            <option value="Beroperasi" <?= (old('status') ?? $series['status'] ?? '') === 'Beroperasi' ? 'selected' : '' ?>>Beroperasi</option>
            <option value="Rusak" <?= (old('status') ?? $series['status'] ?? '') === 'Rusak' ? 'selected' : '' ?>>Rusak</option>
            <option value="Tidak Aktif" <?= (old('status') ?? $series['status'] ?? '') === 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-2">Tahun Pengadaan</label>
          <input type="text" name="tahun" value="<?= esc(old('tahun') ?? $series['tahun'] ?? '') ?>"
                 class="w-full px-4 py-2.5 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:bg-white transition-all">
        </div>
      </div>
    </div>

    <div class="flex items-center gap-4 mt-8">
      <button type="submit"
              class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5">
        <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Series' ?>
      </button>
      <a href="/ipsrs/aset/<?= esc($aset['id']) ?>" 
         class="px-6 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
        Batal
      </a>
    </div>
  </form>
</div>
<?= $this->endSection() ?>
