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
    <h2 class="text-sm font-bold text-slate-800 mb-6 pb-3 border-b border-slate-200">Data Aset</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- Nomor Aset -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Nomor Aset / Kode RS <span class="text-red-500">*</span></label>
        <input type="text" name="nomor_aset" value="<?= old_val($a, 'nomor_aset') ?>" required
               placeholder="Contoh: M-BDG-001"
               class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>

      <!-- Nama -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Nama Aset <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="<?= old_val($a, 'nama') ?>" required
               placeholder="Contoh: AC Split 1 PK"
               class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>

      <!-- Jenis -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Jenis <span class="text-red-500">*</span></label>
        <select name="jenis" required
                class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors appearance-none">
          <option value="">-- Pilih Jenis --</option>
          <?php foreach (['Sarana', 'Prasarana', 'Alat Non Medis'] as $opt): ?>
          <option value="<?= $opt ?>" <?= old_val($a, 'jenis') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Kategori -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Kategori <span class="text-red-500">*</span></label>
        <select name="kategori" required
                class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors appearance-none">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach (($kategoriAset ?? []) as $k): ?>
          <option value="<?= esc($k['nama_kategori'] ?? '') ?>"
                  <?= old_val($a, 'kategori') === ($k['nama_kategori'] ?? '') ? 'selected' : '' ?>>
            <?= esc($k['nama_kategori'] ?? '') ?>
          </option>
          <?php endforeach; ?>
        </select>
        <p class="text-[11px] font-medium text-slate-400 mt-1.5">Kategori tidak ada? <button type="button" onclick="openKategoriModal()" class="text-indigo-600 hover:underline">Tambah Kategori Baru</button></p>
      </div>

      <!-- Lokasi -->
      

      <!-- Gedung -->
      

      <!-- Lantai -->
      

      <!-- Ruangan -->
      

      <!-- Unit -->
      

      <!-- Merk -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Merk</label>
        <input type="text" name="merk" value="<?= old_val($a, 'merk') ?>"
               placeholder="Contoh: Daikin, Philips"
               class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>

      <!-- Model -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Model</label>
        <input type="text" name="model" value="<?= old_val($a, 'model') ?>"
               placeholder="Nomor model"
               class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>

      <!-- No Seri -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">No. Seri</label>
        <input type="text" name="no_seri" value="<?= old_val($a, 'no_seri') ?>"
               placeholder="Nomor seri / serial number"
               class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors font-mono">
      </div>



      <!-- Tahun -->
      

      <!-- Kondisi -->
      

      <!-- Keterangan -->
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Keterangan</label>
        <textarea name="keterangan" rows="4"
                  placeholder="Catatan tambahan (opsional)"
                  class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors resize-none"><?= old_val($a, 'keterangan') ?></textarea>
      </div>

    </div>
  </div>

  <!-- Actions -->
  <div class="flex items-center gap-4">
    <button type="submit"
            class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow-sm transition-all flex items-center gap-2">
      Simpan Aset
    </button>
    <a href="/ipsrs/aset" class="px-6 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white rounded-md border border-slate-200 hover:bg-slate-50 hover:shadow-sm transition-all">
      Batal
    </a>
  </div>
  </div>
</form>

<!-- Modal Tambah Kategori -->
<div id="kategoriModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="kategoriModalPanel">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-lg font-bold text-slate-800">Tambah Kategori Baru</h3>
      <button type="button" onclick="closeKategoriModal()" class="text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="p-6">
      <form id="formKategori" onsubmit="submitKategori(event)">
        <?= csrf_field() ?>
        <div class="mb-4">
          <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Nama Kategori <span class="text-red-500">*</span></label>
          <input type="text" name="nama_kategori" required
                 class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
        </div>
        <div class="mb-6">
          <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Deskripsi</label>
          <input type="text" name="deskripsi"
                 class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" onclick="closeKategoriModal()" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-md hover:bg-slate-50 transition-colors">Batal</button>
          <button type="submit" id="btnSubmitKategori" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors flex items-center gap-2">
            Simpan Kategori
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function openKategoriModal() {
    const modal = document.getElementById('kategoriModal');
    const panel = document.getElementById('kategoriModalPanel');
    modal.classList.remove('hidden');
    // slight delay for transition
    setTimeout(() => {
      modal.classList.remove('opacity-0');
      panel.classList.remove('scale-95');
    }, 10);
  }

  function closeKategoriModal() {
    const modal = document.getElementById('kategoriModal');
    const panel = document.getElementById('kategoriModalPanel');
    modal.classList.add('opacity-0');
    panel.classList.add('scale-95');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.getElementById('formKategori').reset();
    }, 300);
  }

  async function submitKategori(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitKategori');
    const form = document.getElementById('formKategori');
    const formData = new FormData(form);

    // Provide visual feedback
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;

    try {
      const res = await fetch('/ipsrs/kategori-aset/tambah', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      });
      const data = await res.json();
      
      if (data.success) {
        // Append to dropdown and select it
        const select = document.querySelector('select[name="kategori"]');
        const newOption = new Option(data.kategori, data.kategori, true, true);
        select.add(newOption);
        closeKategoriModal();
        
        // Show success alert
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: 'Kategori baru berhasil ditambahkan.',
          timer: 1500,
          showConfirmButton: false
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: data.message || 'Gagal menyimpan kategori'
        });
      }
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Terjadi kesalahan jaringan atau server.'
      });
    } finally {
      btn.disabled = false;
      btn.innerHTML = originalText;
    }
  }
</script>
