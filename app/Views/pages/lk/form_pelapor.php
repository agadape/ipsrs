<div class="max-w-3xl mx-auto">
  <!-- Page Header -->
  <div class="flex items-center gap-4 mb-8">
    <a href="/ipsrs/lk"
       class="w-10 h-10 flex items-center justify-center rounded-md bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700 shadow-sm">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </a>
    <div>
      <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Laporkan Kerusakan</h1>
      <p class="text-sm font-medium text-slate-500 mt-1">Kami akan segera menangani masalah Anda.</p>
    </div>
  </div>

  <form method="POST" action="/ipsrs/lk/baru" class="space-y-6">
    <?= csrf_field() ?>
    
    <!-- Hidden Auto-filled fields -->
    <input type="hidden" name="tanggal" value="<?= old('tanggal') ?? date('Y-m-d') ?>">
    <input type="hidden" name="jam_laporan" value="<?= old('jam_laporan') ?? date('H:i') ?>">

    <!-- Main Card -->
    <div class="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
      
      <!-- Banner/Hero Section inside card -->
      <div class="bg-slate-50 p-8 border-b border-slate-200 relative overflow-hidden">
        <div class="relative z-10">
          <h2 class="text-lg font-bold text-slate-800 tracking-tight mb-2">Formulir Tiket Bantuan</h2>
          <p class="text-slate-600 text-sm max-w-md leading-relaxed font-medium">Silakan lengkapi data di bawah ini dengan jelas agar tim teknisi IPSRS dapat membawa peralatan yang tepat.</p>
        </div>
      </div>

      <div class="p-8 space-y-8 bg-white">
        
        <!-- Section 1: Who & Where -->
        <div>
          <h3 class="text-xs font-bold tracking-widest text-red-700 uppercase mb-5 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Data Pelapor
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Nama Pelapor <span class="text-red-500">*</span></label>
              <input type="text" name="pelapor" value="<?= esc(old('pelapor') ?? session('user_name') ?? '') ?>" required
                     class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Unit / Instalasi <span class="text-red-500">*</span></label>
              <select name="unit_pelapor" required
                      class="select2 w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
                <option value="">-- Pilih Unit / Instalasi --</option>
                <?php foreach (getStandardUnits() as $u): ?>
                  <option value="<?= esc($u) ?>" <?= old('unit_pelapor') === $u ? 'selected' : '' ?>><?= esc($u) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Lokasi Spesifik <span class="text-red-500">*</span></label>
              <select name="lokasi" required
                      class="select2 w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
                <option value="">-- Pilih Unit / Lokasi --</option>
                <?php foreach (getStandardUnits() as $u): ?>
                  <option value="<?= esc($u) ?>" <?= old('lokasi') === $u ? 'selected' : '' ?>><?= esc($u) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <hr class="border-slate-100">

        <!-- Section 2: What's wrong -->
        <div>
          <h3 class="text-xs font-bold tracking-widest text-red-700 uppercase mb-5 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Detail Masalah
          </h3>
          <div class="space-y-6">
            <div>
              <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Nama Aset / Alat <span class="text-slate-400 font-normal normal-case">(Opsional)</span></label>
              <input type="text" name="nama_aset" value="<?= esc(old('nama_aset') ?? '') ?>"
                     placeholder="Cth: AC Daikin, Bed Pasien, Lampu"
                     class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Deskripsi Kerusakan <span class="text-red-500">*</span></label>
              <textarea name="keluhan" rows="4" required
                        placeholder="Jelaskan masalah secara detail. Contoh: AC meneteskan air lumayan deras di atas kasur pasien..."
                        class="w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors resize-none"></textarea>
            </div>
          </div>
        </div>

      </div>
      
      <!-- Footer Actions -->
      <div class="bg-slate-50 p-6 sm:px-8 border-t border-slate-200 flex items-center justify-end gap-4">
        <a href="/ipsrs/lk" class="px-6 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white rounded-md border border-slate-200 hover:bg-slate-50 hover:shadow-sm transition-all">
          Batal
        </a>
        <button type="submit"
                class="px-8 py-2.5 bg-red-700 hover:bg-red-800 text-white text-sm font-semibold rounded-md shadow-sm transition-all flex items-center gap-2">
          Kirim Laporan
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
      </div>

    </div>
  </form>
</div>
