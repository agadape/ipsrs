<div class="max-w-3xl mx-auto">
  <!-- Page Header -->
  <div class="flex items-center gap-4 mb-8">
    <a href="/ipsrs/lk"
       class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white shadow-sm border border-gray-100 hover:border-gray-200 transition-colors text-gray-500 hover:text-gray-700">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </a>
    <div>
      <h1 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Laporkan Kerusakan</h1>
      <p class="text-sm text-gray-500 mt-1">Kami akan segera menangani masalah Anda.</p>
    </div>
  </div>

  <form method="POST" action="/ipsrs/lk/baru" class="space-y-6">
    <?= csrf_field() ?>
    
    <!-- Hidden Auto-filled fields -->
    <input type="hidden" name="tanggal" value="<?= old('tanggal') ?? date('Y-m-d') ?>">
    <input type="hidden" name="jam_laporan" value="<?= old('jam_laporan') ?? date('H:i') ?>">

    <!-- Main Card -->
    <div class="card overflow-hidden shadow-xl shadow-indigo-100/20 border-0 ring-1 ring-gray-100">
      
      <!-- Banner/Hero Section inside card -->
      <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-8 text-white relative overflow-hidden">
        <div class="relative z-10">
          <h2 class="text-xl font-bold mb-2">Formulir Tiket Bantuan</h2>
          <p class="text-indigo-100 text-sm max-w-md leading-relaxed">Silakan lengkapi data di bawah ini dengan jelas agar tim teknisi IPSRS dapat membawa peralatan yang tepat.</p>
        </div>
        <!-- Decorative circles -->
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white rounded-full opacity-10 blur-2xl"></div>
        <div class="absolute bottom-0 right-10 -mb-4 w-24 h-24 bg-white rounded-full opacity-10 blur-xl"></div>
      </div>

      <div class="p-8 space-y-8 bg-white">
        
        <!-- Section 1: Who & Where -->
        <div>
          <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase mb-5 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Data Pelapor
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-2">Nama Pelapor <span class="text-red-500">*</span></label>
              <input type="text" name="pelapor" value="<?= esc(old('pelapor') ?? session('user_name') ?? '') ?>" required
                     class="w-full px-4 py-3 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all shadow-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-2">Unit / Instalasi <span class="text-red-500">*</span></label>
              <input type="text" name="unit_pelapor" value="<?= esc(old('unit_pelapor') ?? '') ?>" required
                     placeholder="Cth: IGD, Poli Gigi"
                     class="w-full px-4 py-3 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all shadow-sm">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-gray-600 mb-2">Lokasi Spesifik <span class="text-red-500">*</span></label>
              <input type="text" name="lokasi" value="<?= esc(old('lokasi') ?? '') ?>" required
                     placeholder="Cth: Ruang Tindakan Bedah 1, Pojok Kanan"
                     class="w-full px-4 py-3 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all shadow-sm">
            </div>
          </div>
        </div>

        <hr class="border-gray-100">

        <!-- Section 2: What's wrong -->
        <div>
          <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase mb-5 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Detail Masalah
          </h3>
          <div class="space-y-6">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-2">Nama Aset / Alat <span class="text-gray-400 font-normal">(Opsional)</span></label>
              <input type="text" name="nama_aset" value="<?= esc(old('nama_aset') ?? '') ?>"
                     placeholder="Cth: AC Daikin, Bed Pasien, Lampu"
                     class="w-full px-4 py-3 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all shadow-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-2">Deskripsi Kerusakan <span class="text-red-500">*</span></label>
              <textarea name="keluhan" rows="4" required
                        placeholder="Jelaskan masalah secara detail. Contoh: AC meneteskan air lumayan deras di atas kasur pasien..."
                        class="w-full px-4 py-3 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all shadow-sm resize-none"></textarea>
            </div>
          </div>
        </div>

      </div>
      
      <!-- Footer Actions -->
      <div class="bg-gray-50 p-6 sm:px-8 border-t border-gray-100 flex items-center justify-end gap-4">
        <a href="/ipsrs/lk" class="px-6 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 bg-white rounded-xl border border-gray-200 hover:bg-gray-50 hover:shadow-sm transition-all">
          Batal
        </a>
        <button type="submit"
                class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-bold rounded-xl shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all flex items-center gap-2">
          Kirim Laporan
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
      </div>

    </div>
  </form>
</div>
