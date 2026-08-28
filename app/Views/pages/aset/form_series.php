
<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs/aset/<?= esc($aset['id']) ?>"
     class="w-8 h-8 flex items-center justify-center rounded-md bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight"><?= $isEdit ? 'Edit Series' : 'Tambah Series Baru' ?></h1>
    <p class="text-sm font-medium text-slate-500 mt-1">Katalog: <?= esc($aset['nama'] ?? '') ?></p>
  </div>
</div>

<div class="card p-6">
  <form method="POST" action="<?= $isEdit ? '/ipsrs/aset/series/' . esc($series['id']) . '/edit' : '/ipsrs/aset/tambah-series/' . esc($aset['id']) ?>">
    <?= csrf_field() ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nomor Aset / Inventaris <span class="text-red-500">*</span></label>
        <input type="text" name="nomor_aset" value="<?= esc(old('nomor_aset') ?? $series['nomor_aset'] ?? '') ?>" required
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nomor Seri (S/N)</label>
        <input type="text" name="no_seri" value="<?= esc(old('no_seri') ?? $series['no_seri'] ?? '') ?>"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Merk</label>
        <input type="text" name="merk" value="<?= esc(old('merk') ?? $series['merk'] ?? '') ?>"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Model / Tipe</label>
        <input type="text" name="model" value="<?= esc(old('model') ?? $series['model'] ?? '') ?>"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kapasitas</label>
        <input type="text" name="kapasitas" value="<?= esc(old('kapasitas') ?? $series['kapasitas'] ?? '') ?>"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>
    </div>

    <div class="mb-6">
      <h3 class="text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Lokasi Penempatan</h3>
      <div class="grid grid-cols-1 gap-6">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Pilih Lokasi Ruangan / Unit <span class="text-red-500">*</span></label>
          <select name="id_lokasi" required
                 class="select2 w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
            <option value="">-- Pilih Lokasi --</option>
            <?php foreach ($masterLokasi as $ml): ?>
              <?php $label = $ml['gedung'] . ' - ' . ($ml['lantai'] ? $ml['lantai'] . ' - ' : '') . $ml['nama_ruangan'] . ' (' . $ml['nama_unit'] . ')'; ?>
              <option value="<?= esc($ml['id']) ?>" <?= (old('id_lokasi') ?? $series['id_lokasi'] ?? '') === $ml['id'] ? 'selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach; ?>
          </select>
          <p class="text-xs text-slate-500 mt-1">Gedung - Lantai - Ruangan (Unit)</p>
        </div>
      </div>
    </div>

    <div class="mb-6">
      <h3 class="text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Status & Fisik</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kondisi</label>
          <select name="kondisi" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm appearance-none transition-colors">
            <option value="Baik" <?= (old('kondisi') ?? $series['kondisi'] ?? '') === 'Baik' ? 'selected' : '' ?>>Baik</option>
            <option value="Rusak Ringan" <?= (old('kondisi') ?? $series['kondisi'] ?? '') === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
            <option value="Rusak Berat" <?= (old('kondisi') ?? $series['kondisi'] ?? '') === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Status</label>
          <select name="status" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm appearance-none transition-colors">
            <option value="Tersedia" <?= (old('status') ?? $series['status'] ?? '') === 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
            <option value="Dalam Perbaikan" <?= (old('status') ?? $series['status'] ?? '') === 'Dalam Perbaikan' ? 'selected' : '' ?>>Dalam Perbaikan</option>
            <option value="Rusak Berat" <?= (old('status') ?? $series['status'] ?? '') === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tahun Pengadaan</label>
          <input type="number" name="tahun_perolehan" value="<?= esc(old('tahun_perolehan') ?? $series['tahun_perolehan'] ?? '') ?>"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
        </div>
      </div>
    </div>

    <div class="flex items-center gap-4 mt-8">
      <button type="submit"
              class="px-6 py-2.5 bg-red-700 hover:bg-red-800 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
        <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Series' ?>
      </button>
      <a href="/ipsrs/aset/<?= esc($aset['id']) ?>" 
         class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 rounded-md transition-colors shadow-sm">
        Batal
      </a>
    </div>
  </form>
</div>



