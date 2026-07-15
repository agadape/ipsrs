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

      <!-- Lokasi -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi <span class="text-red-500">*</span></label>
        <input type="text" name="lokasi" value="<?= esc(old('lokasi') ?? '') ?>" required
               placeholder="Gedung / Ruangan / Area"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Nama Aset -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aset yang Rusak <span class="text-gray-400 font-normal">(jika tahu)</span></label>
        <input type="text" name="nama_aset" value="<?= esc(old('nama_aset') ?? '') ?>"
               placeholder="Nama alat atau barang"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

    </div>
  </div>

  <!-- Actions -->
  <div class="flex items-center gap-3">
    <button type="submit"
            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
      Kirim Laporan
    </button>
    <a href="/ipsrs/lk" class="px-5 py-2.5 text-sm text-gray-500 hover:text-gray-700 bg-white rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
      Batal
    </a>
  </div>
</form>
