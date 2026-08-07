<?php
$isEdit = $isEdit ?? false;
$a = $aset ?? [];
$action = $isEdit ? "/ipsrs/aset/".esc($a["id"] ?? "")."/edit" : "/ipsrs/aset/tambah";
$title  = $isEdit ? "Edit Katalog Aset" : "Tambah Katalog Aset Baru";
$sub    = $isEdit ? "Perbarui informasi katalog (tipe logis) aset" : "Daftarkan tipe/kategori aset baru ke sistem";

function old_val(array $a, string $key, string $default = ""): string {
    $post = old($key);
    return esc($post !== null ? $post : ($a[$key] ?? $default));
}
?>

<!-- Page Header -->
<div class="flex items-center gap-4 mb-8">
  <a href="/ipsrs/aset"
     class="w-10 h-10 flex items-center justify-center rounded-md bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700 shadow-sm">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
  </a>
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight"><?= $title ?></h1>
    <p class="text-sm font-medium text-slate-500 mt-1"><?= $sub ?></p>
  </div>
</div>

<form method="POST" action="<?= $action ?>">
  <?= csrf_field() ?>

  <div class="bg-white border border-slate-200 rounded-md shadow-sm p-8 mb-6">
    <h2 class="text-sm font-bold text-slate-800 mb-6 pb-3 border-b border-slate-200">Data Master Aset</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Nama -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Nama / Tipe Aset <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="<?= old_val($a, "nama") ?>" required
               placeholder="Contoh: AC Split, Tempat Tidur Pasien"
               class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>

      <!-- Jenis -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Jenis <span class="text-red-500">*</span></label>
        <select name="jenis" required
                class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors appearance-none">
          <option value="">-- Pilih Jenis --</option>
          <?php foreach (["Sarana", "Prasarana", "Alat Non Medis"] as $opt): ?>
          <option value="<?= $opt ?>" <?= old_val($a, "jenis") === $opt ? "selected" : "" ?>><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Kategori -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Kategori <span class="text-red-500">*</span></label>
        <select name="kategori" required
                class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors appearance-none">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach (($kategoriAset ?? []) as $k): ?>
          <option value="<?= esc($k["nama_kategori"] ?? "") ?>"
                  <?= old_val($a, "kategori") === ($k["nama_kategori"] ?? "") ? "selected" : "" ?>>
            <?= esc($k["nama_kategori"] ?? "") ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Keterangan -->
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Keterangan (Opsional)</label>
        <textarea name="keterangan" rows="4"
                  placeholder="Deskripsi umum tentang kategori aset ini"
                  class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors resize-none"><?= old_val($a, "keterangan") ?></textarea>
      </div>
    </div>
  </div>

  <!-- Actions -->
  <div class="flex items-center gap-4">
    <button type="submit"
            class="px-6 py-3 bg-red-700 hover:bg-red-800 text-white text-sm font-medium rounded-md shadow-sm transition-colors flex items-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      Simpan Master
    </button>
    <a href="/ipsrs/aset" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-md shadow-sm transition-colors">
      Batal
    </a>
  </div>
</form>

