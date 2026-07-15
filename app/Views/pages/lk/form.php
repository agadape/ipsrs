<!-- Page Header -->
<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs/lk"
     class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 hover:border-gray-200 transition-colors text-gray-500 hover:text-gray-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
  </a>
  <div>
    <h1 class="text-xl font-bold text-gray-800">Buat Laporan Kerusakan</h1>
    <p class="text-sm text-gray-400 mt-0.5">Isi formulir laporan kerusakan aset</p>
  </div>
</div>

<form method="POST" action="/ipsrs/lk/baru">
  <?= csrf_field() ?>

  <div class="card p-6 mb-5">
    <h2 class="text-sm font-semibold text-gray-700 mb-5 pb-3 border-b border-gray-100">Identifikasi Pelapor</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

      <!-- Tanggal -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" value="<?= old('tanggal') ?? date('Y-m-d') ?>" required
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Jam Laporan -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Laporan <span class="text-red-500">*</span></label>
        <input type="time" name="jam_laporan" value="<?= old('jam_laporan') ?? date('H:i') ?>" required
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Pelapor -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pelapor <span class="text-red-500">*</span></label>
        <input type="text" name="pelapor" value="<?= esc(old('pelapor') ?? session('user_name') ?? '') ?>" required
               placeholder="Nama lengkap pelapor"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Unit Pelapor -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Unit Pelapor <span class="text-red-500">*</span></label>
        <input type="text" name="unit_pelapor" value="<?= esc(old('unit_pelapor') ?? '') ?>" required
               placeholder="Contoh: IGD, ICU, Poli Umum"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

    </div>
  </div>

  <div class="card p-6 mb-5">
    <h2 class="text-sm font-semibold text-gray-700 mb-5 pb-3 border-b border-gray-100">Detail Kerusakan</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

      <!-- Keluhan -->
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keluhan <span class="text-red-500">*</span></label>
        <textarea name="keluhan" rows="3" required
                  placeholder="Deskripsikan keluhan atau kerusakan yang dilaporkan..."
                  class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 resize-none"><?= esc(old('keluhan') ?? '') ?></textarea>
      </div>

      <?php if (session('user_role') !== 'pelapor'): ?>
      <!-- Kode -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Pekerjaan <span class="text-red-500">*</span></label>
        <select name="kode" required
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Kode --</option>
          <?php foreach (($kodeKerusakan ?? []) as $kk): ?>
          <option value="<?= esc($kk['kode'] ?? '') ?>"
                  <?= old('kode') === ($kk['kode'] ?? '') ? 'selected' : '' ?>>
            <?= esc(($kk['kode'] ?? '').' — '.($kk['nama'] ?? '')) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <p class="text-[11px] text-gray-400 mt-1">Kode tidak ada? Tambahkan di <a href="/ipsrs/kode-kerusakan" class="text-indigo-500 hover:underline">Menu Kode Kerusakan</a></p>
      </div>
      <?php endif; ?>

      <!-- Lokasi -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi <span class="text-red-500">*</span></label>
        <input type="text" name="lokasi" value="<?= esc(old('lokasi') ?? '') ?>" required
               placeholder="Gedung / Ruangan / Area"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <?php if (session('user_role') !== 'pelapor'): ?>
      <!-- Aset (optional) -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aset Terkait <span class="text-gray-400 font-normal">(opsional)</span></label>
        <select name="id_aset"
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Aset --</option>
          <?php foreach (($aset ?? []) as $a): ?>
          <option value="<?= esc($a['id'] ?? '') ?>"
                  data-lokasi="<?= esc($a['lokasi'] ?? '') ?>"
                  <?= old('id_aset') == ($a['id'] ?? '') ? 'selected' : '' ?>>
            <?= esc(($a['id'] ?? '').' — '.($a['nama'] ?? '')) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>

      <!-- Nama Aset -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Aset <span class="text-gray-400 font-normal">(jika tidak ada di daftar)</span></label>
        <input type="text" name="nama_aset" value="<?= esc(old('nama_aset') ?? '') ?>"
               placeholder="Nama aset manual"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

    </div>
  </div>

  <!-- Actions -->
  <div class="flex items-center gap-3">
    <button type="submit"
            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
      Buat LK
    </button>
    <a href="/ipsrs/lk" class="px-5 py-2.5 text-sm text-gray-500 hover:text-gray-700 bg-white rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
      Batal
    </a>
  </div>

  <div id="lokasi-warning" class="hidden mt-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
    ⚠️ <strong>Perhatian:</strong> Aset ini tercatat di <span id="lokasi-terdaftar" class="font-mono font-semibold"></span>,
    tapi lokasi laporan berbeda. Apakah aset sudah berpindah?
    <label class="flex items-center gap-2 mt-2 cursor-pointer">
      <input type="checkbox" name="update_lokasi_aset" value="1" class="rounded">
      <span>Ya, perbarui lokasi aset sesuai lokasi laporan ini</span>
    </label>
  </div>
</form>

<script>
(function() {
  var selAset   = document.querySelector('select[name="id_aset"]');
  var inpLokasi = document.querySelector('input[name="lokasi"]');
  var warning   = document.getElementById('lokasi-warning');
  var spanLok   = document.getElementById('lokasi-terdaftar');

  function check() {
    if (!selAset || !inpLokasi || !warning) return;
    var opt = selAset.options[selAset.selectedIndex];
    var lokasiAset = opt ? (opt.dataset.lokasi || '') : '';
    var lokasiLaporan = inpLokasi.value.trim();

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
