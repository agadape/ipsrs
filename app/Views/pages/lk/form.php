<!-- Page Header -->
<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs/lk"
     class="w-8 h-8 flex items-center justify-center rounded-md bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
  </a>
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Buat Laporan Kerusakan</h1>
    <p class="text-sm text-slate-500 mt-1">Isi formulir laporan kerusakan aset</p>
  </div>
</div>

<form method="POST" action="/ipsrs/lk/baru">
  <?= csrf_field() ?>

  <div class="card p-6 mb-6">
    <h2 class="text-sm font-bold text-slate-800 mb-5 pb-3 border-b border-slate-200">Identifikasi Pelapor</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

      <!-- Tanggal -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" value="<?= old('tanggal') ?? date('Y-m-d') ?>" required
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
      </div>

      <!-- Jam Laporan -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Jam Laporan <span class="text-red-500">*</span></label>
        <input type="time" name="jam_laporan" value="<?= old('jam_laporan') ?? date('H:i') ?>" required
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
      </div>

      <!-- Pelapor -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Pelapor <span class="text-red-500">*</span></label>
        <input type="text" name="pelapor" value="<?= esc(old('pelapor') ?? session('user_name') ?? '') ?>" required
               placeholder="Nama lengkap pelapor"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
      </div>

      <!-- Unit Pelapor -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Unit Pelapor <span class="text-red-500">*</span></label>
        <select name="unit_pelapor" required
                class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
          <option value="">-- Pilih Unit --</option>
          <?php foreach (getStandardUnits() as $u): ?>
            <option value="<?= esc($u) ?>" <?= old('unit_pelapor') === $u ? 'selected' : '' ?>><?= esc($u) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

    </div>
  </div>

  <div class="card p-6 mb-6">
    <h2 class="text-sm font-bold text-slate-800 mb-5 pb-3 border-b border-slate-200">Detail Kerusakan</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

      <!-- Keluhan -->
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Keluhan <span class="text-red-500">*</span></label>
        <textarea name="keluhan" rows="3" required
                  placeholder="Deskripsikan keluhan atau kerusakan yang dilaporkan..."
                  class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm resize-none"><?= esc(old('keluhan') ?? '') ?></textarea>
      </div>

      <!-- Kode -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kode Pekerjaan <span class="text-red-500">*</span></label>
        <select name="kode" required
                class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm appearance-none">
          <option value="">-- Pilih Kode --</option>
          <?php foreach (($kodeKerusakan ?? []) as $kk): ?>
          <option value="<?= esc($kk['kode'] ?? '') ?>"
                  <?= old('kode') === ($kk['kode'] ?? '') ? 'selected' : '' ?>>
            <?= esc(($kk['kode'] ?? '').' — '.($kk['nama'] ?? '')) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <p class="text-[11px] text-slate-400 mt-1.5 font-medium">Kode tidak ada? Tambahkan di <a href="/ipsrs/kode-kerusakan" class="text-indigo-600 hover:underline">Menu Kode Kerusakan</a></p>
      </div>

      <!-- Lokasi -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Lokasi <span class="text-red-500">*</span></label>
        <select name="lokasi" required
                class="select2 w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
          <option value="">-- Pilih Unit / Lokasi --</option>
          <?php foreach (getStandardUnits() as $u): ?>
            <option value="<?= esc($u) ?>" <?= old('lokasi') === $u ? 'selected' : '' ?>><?= esc($u) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Aset (optional) -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Aset Terkait <span class="text-slate-400 font-normal lowercase">(opsional)</span></label>
        <select name="id_aset" id="id_aset" onchange="updateAsetInfo()"
                class="select2 w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
          <option value="">-- Pilih Aset --</option>
          <?php foreach (($aset ?? []) as $a): ?>
            <option value="<?= esc($a['id'] ?? '') ?>"
                    data-lokasi="<?= esc($a['lokasi'] ?? '') ?>"
                    <?= old('id_aset') == ($a['id'] ?? '') ? 'selected' : '' ?>>
              <?= esc(format_aset_label($a)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Nama Aset -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Aset <span class="text-slate-400 font-normal lowercase">(jika tidak ada di daftar)</span></label>
        <input type="text" name="nama_aset" value="<?= esc(old('nama_aset') ?? '') ?>"
               placeholder="Nama aset manual"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
      </div>

    </div>
  </div>

  <!-- Actions -->
  <div class="flex items-center gap-4">
    <button type="submit"
            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
      Buat Laporan Kerusakan
    </button>
    <a href="/ipsrs/lk" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 rounded-md transition-colors shadow-sm">
      Batal
    </a>
  </div>

  <div id="lokasi-warning" class="hidden mt-4 p-4 bg-amber-50 border border-amber-200 rounded-md text-sm text-amber-800">
    <span class="font-bold">⚠️ Perhatian:</span> Aset ini tercatat di <span id="lokasi-terdaftar" class="font-mono font-semibold"></span>,
    tapi lokasi laporan berbeda. Apakah aset sudah berpindah?
    <label class="flex items-center gap-2 mt-3 cursor-pointer">
      <input type="checkbox" name="update_lokasi_aset" value="1" class="rounded border-amber-300 text-indigo-600 focus:ring-indigo-500">
      <span class="font-medium">Ya, perbarui lokasi aset sesuai lokasi laporan ini</span>
    </label>
  </div>
</form>

<script>
(function() {
  var selAset   = document.querySelector('select[name="id_aset"]');
  var selLokasi = $('select[name="lokasi"]');
  var warning   = document.getElementById('lokasi-warning');
  var spanLok   = document.getElementById('lokasi-terdaftar');

  function updateAsetInfo() {
    var sel = document.getElementById('id_aset');
    var opt = sel.options[sel.selectedIndex];
    if (opt && opt.value !== "") {
      var asetUnit = opt.getAttribute('data-lokasi'); 
      if(asetUnit) {
          selLokasi.val(asetUnit).trigger('change');
      }
    }
  }

  function check() {
    if (!selAset || !selLokasi || !warning) return;
    var opt = selAset.options[selAset.selectedIndex];
    var lokasiAset = opt ? (opt.dataset.lokasi || '') : '';
    var lokasiLaporan = selLokasi.val();

    if (lokasiAset && lokasiLaporan && lokasiLaporan !== lokasiAset) {
      spanLok.textContent = lokasiAset;
      warning.classList.remove('hidden');
    } else {
      warning.classList.add('hidden');
    }

    // Auto-fill lokasi from aset if lokasi field is empty
    if (lokasiAset && !lokasiLaporan) {
      inpLokasi.value = lokasiAset;
    }
  }

  if (selAset)   selAset.addEventListener('change', check);
  if (inpLokasi) inpLokasi.addEventListener('input', check);
  check();
})();
</script>

