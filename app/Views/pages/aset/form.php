<?php
$isEdit = $isEdit ?? false;
$a = $aset ?? [];
$action = $isEdit ? '/ipsrs/aset/'.esc($a['id'] ?? '').'/edit' : '/ipsrs/aset/tambah';
$title  = $isEdit ? 'Edit Aset' : 'Tambah Aset Baru';
$sub    = $isEdit ? 'Perbarui informasi aset yang sudah ada' : 'Daftarkan aset baru ke sistem inventaris';

function old_val(array $a, string $key, string $default = ''): string {
    $post = old($key);
    return esc($post !== null ? $post : ($a[$key] ?? $default));
}
?>

<!-- Page Header -->
<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs/aset"
     class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#121620]/60 shadow-[0_4px_20px_rgba(0,0,0,0.5)] border border-white/5 hover:border-gray-200 transition-colors text-gray-400 hover:text-gray-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
  </a>
  <div>
    <h1 class="text-xl font-bold text-gray-100"><?= $title ?></h1>
    <p class="text-sm text-gray-400 mt-0.5"><?= $sub ?></p>
  </div>
</div>

<form method="POST" action="<?= $action ?>">
  <?= csrf_field() ?>

  <div class="card p-6 mb-5">
    <h2 class="text-sm font-semibold text-gray-200 mb-5 pb-3 border-b border-white/5">Data Aset</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

      <!-- Nomor Aset -->
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Nomor Aset / Kode RS <span class="text-red-500">*</span></label>
        <input type="text" name="nomor_aset" value="<?= old_val($a, 'nomor_aset') ?>" required
               placeholder="Contoh: M-BDG-001"
               class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-shadow shadow-inner">
      </div>

      <!-- Nama -->
      <div>
        <label class="block text-xs font-bold text-gray-200 mb-2 uppercase tracking-wide">Nama Aset <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="<?= old_val($a, 'nama') ?>" required
               placeholder="Contoh: AC Split 1 PK"
               class="w-full px-4 py-3 text-[15px] bg-[#181C25]/80 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-shadow shadow-inner">
      </div>

      <!-- Jenis -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Jenis <span class="text-red-500">*</span></label>
        <select name="jenis" required
                class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Jenis --</option>
          <?php foreach (['Sarana', 'Prasarana', 'Alat Non Medis'] as $opt): ?>
          <option value="<?= $opt ?>" <?= old_val($a, 'jenis') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Kategori -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kategori <span class="text-red-500">*</span></label>
        <select name="kategori" required
                class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach (($kategoriAset ?? []) as $k): ?>
          <option value="<?= esc($k['nama_kategori'] ?? '') ?>"
                  <?= old_val($a, 'kategori') === ($k['nama_kategori'] ?? '') ? 'selected' : '' ?>>
            <?= esc($k['nama_kategori'] ?? '') ?>
          </option>
          <?php endforeach; ?>
        </select>
        <p class="text-[11px] text-gray-400 mt-1">Kategori tidak ada? Tambahkan di <a href="/ipsrs/kategori-aset" class="text-indigo-500 hover:underline">Menu Kategori Aset</a></p>
      </div>

      <!-- Lokasi -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Lokasi <span class="text-red-500">*</span></label>
        <input type="text" name="lokasi" value="<?= old_val($a, 'lokasi') ?>" required
               placeholder="Contoh: Gedung A - Lantai 2"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Gedung -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Gedung <span class="text-red-500">*</span></label>
        <input type="text" name="gedung" value="<?= old_val($a, 'gedung') ?>" required
               placeholder="Contoh: Gedung A"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Lantai -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Lantai</label>
        <input type="text" name="lantai" value="<?= old_val($a, 'lantai') ?>"
               placeholder="Contoh: 2"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Ruangan -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Ruangan <span class="text-red-500">*</span></label>
        <input type="text" name="ruangan" value="<?= old_val($a, 'ruangan') ?>" required
               placeholder="Contoh: Ruang Operasi"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Unit -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Unit <span class="text-red-500">*</span></label>
        <input type="text" name="unit" value="<?= old_val($a, 'unit') ?>" required
               placeholder="Contoh: IGD, ICU, Radiologi"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Merk -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Merk</label>
        <input type="text" name="merk" value="<?= old_val($a, 'merk') ?>"
               placeholder="Contoh: Daikin, Philips"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Model -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Model</label>
        <input type="text" name="model" value="<?= old_val($a, 'model') ?>"
               placeholder="Nomor model"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- No Seri -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">No. Seri</label>
        <input type="text" name="no_seri" value="<?= old_val($a, 'no_seri') ?>"
               placeholder="Nomor seri / serial number"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Kapasitas -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kapasitas</label>
        <input type="text" name="kapasitas" value="<?= old_val($a, 'kapasitas') ?>"
               placeholder="Contoh: 1 PK, 5000 VA"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Tahun -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Tahun Pengadaan</label>
        <input type="number" name="tahun" value="<?= old_val($a, 'tahun') ?>"
               min="1990" max="<?= date('Y') + 1 ?>" placeholder="<?= date('Y') ?>"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Kondisi -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kondisi <span class="text-red-500">*</span></label>
        <select name="kondisi" required
                class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Kondisi --</option>
          <?php foreach (['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'] as $opt): ?>
          <option value="<?= $opt ?>" <?= old_val($a, 'kondisi') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Keterangan -->
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Keterangan</label>
        <textarea name="keterangan" rows="3"
                  placeholder="Catatan tambahan (opsional)"
                  class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 resize-none"><?= old_val($a, 'keterangan') ?></textarea>
      </div>

    </div>
  </div>

  <!-- Actions -->
  <div class="flex items-center gap-3">
    <button type="submit"
            class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 text-white text-[15px] font-bold rounded-2xl transition-all duration-300">
      Simpan Aset
    </button>
    <a href="/ipsrs/aset" class="px-6 py-3 text-[15px] font-semibold text-gray-400 hover:text-gray-800 bg-[#121620]/60 rounded-2xl border border-white/10 hover:bg-white/5 hover:shadow-sm transition-all">
      Batal
    </a>
  </div>
</form>
