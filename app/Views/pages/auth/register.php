<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Masuk — IPSRS RSUD Kota Yogyakarta</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    @keyframes blob { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(30px,-20px) scale(1.05)} 66%{transform:translate(-15px,25px) scale(.95)} }
    .blob { animation: blob 8s infinite ease-in-out; }
    .blob2 { animation: blob 10s infinite ease-in-out reverse; }
    .blob3 { animation: blob 12s infinite ease-in-out 2s; }
    @keyframes gradient-x { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
    .animate-gradient-x { animation: gradient-x 3s ease infinite; background-size: 200% auto; }
  </style>
</head>
<body class="h-screen flex overflow-hidden" style="background:#0A0F1E">

  <!-- ── Left panel ─────────────────────────────────────────────────── -->
  <div class="relative hidden lg:flex flex-col w-[56%] px-14 py-12 overflow-hidden" style="background:#0A0F1E">

    <!-- Blobs -->
    <div class="blob pointer-events-none absolute -top-32 -left-32 w-[480px] h-[480px] rounded-full opacity-20 blur-[120px]" style="background:#4F46E5"></div>
    <div class="blob2 pointer-events-none absolute top-1/2 -right-20 w-[320px] h-[320px] rounded-full opacity-20 blur-[100px]" style="background:#7C3AED"></div>
    <div class="blob3 pointer-events-none absolute bottom-0 left-1/3 w-[260px] h-[260px] rounded-full opacity-10 blur-[80px]" style="background:#818CF8"></div>

    <!-- Logo -->
    <div class="relative flex items-center gap-3">
      <div class="flex items-center gap-2 px-4 py-2 rounded-full text-white text-sm font-semibold"
           style="background:linear-gradient(135deg,#4F6EF7,#7C3AED)">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
        </svg>
        IPSRS
      </div>
      <span class="text-gray-500 text-xs">RSUD Kota Yogyakarta</span>
    </div>

    <!-- Headline -->
    <div class="relative mt-16 flex-1 z-10">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-indigo-300 text-[11px] font-bold uppercase tracking-wider mb-6 backdrop-blur-md">
        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse shadow-[0_0_8px_rgba(129,140,248,0.8)]"></span>
        Platform IPSRS v2.0
      </div>
      
      <h1 class="text-5xl lg:text-[54px] font-black text-white leading-[1.15] tracking-tight">
        Sistem Informasi<br>
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-indigo-400 animate-gradient-x">
          Pemeliharaan
        </span><br>
        Sarana &amp; Prasarana
      </h1>
      
      <p class="mt-6 text-gray-400 text-[15px] leading-relaxed max-w-md font-medium">
        Kelola master data aset, pantau respons laporan kerusakan, dan jadwalkan perawatan preventif fasilitas secara cerdas.
      </p>

      <!-- Features list -->
      <ul class="mt-10 space-y-5">
        <!-- Feature 1 -->
        <li class="flex items-center gap-4 group">
          <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-indigo-400 shrink-0 group-hover:bg-teal-500/20 group-hover:text-indigo-300 transition-all duration-300 shadow-inner">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
              <ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
            </svg>
          </div>
          <div>
            <p class="text-white text-sm font-bold tracking-wide">Pangkalan Data Terpusat</p>
            <p class="text-gray-400 text-xs mt-1 leading-relaxed">Kelola ribuan data aset, vendor, dan suku cadang dalam satu sistem pintar.</p>
          </div>
        </li>
        <!-- Feature 2 -->
        <li class="flex items-center gap-4 group">
          <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-purple-400 shrink-0 group-hover:bg-emerald-500/20 group-hover:text-purple-300 transition-all duration-300 shadow-inner">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
            </svg>
          </div>
          <div>
            <p class="text-white text-sm font-bold tracking-wide">Respon Cepat Laporan (LK)</p>
            <p class="text-gray-400 text-xs mt-1 leading-relaxed">Notifikasi real-time dan pelacakan SLA untuk setiap laporan kerusakan.</p>
          </div>
        </li>
        <!-- Feature 3 -->
        <li class="flex items-center gap-4 group">
          <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-emerald-400 shrink-0 group-hover:bg-emerald-500/20 group-hover:text-emerald-300 transition-all duration-300 shadow-inner">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M9 16l2 2 4-4"/>
            </svg>
          </div>
          <div>
            <p class="text-white text-sm font-bold tracking-wide">Otomatisasi Preventif</p>
            <p class="text-gray-400 text-xs mt-1 leading-relaxed">Jadwalkan pemeliharaan rutin secara otomatis untuk mencegah kerusakan.</p>
          </div>
        </li>
      </ul>
    </div>

    <!-- Footer -->
    <div class="relative mt-8 pt-6 border-t border-white/5">
      <div class="flex items-center gap-3">
        <div class="w-1 h-6 rounded-full" style="background:linear-gradient(to bottom,#4F6EF7,#7C3AED)"></div>
        <div>
          <p class="text-white text-xs font-medium">RSUD Kota Yogyakarta</p>
          <p class="text-gray-500 text-xs">Instalasi Pemeliharaan Sarana Rumah Sakit</p>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Right panel ─────────────────────────────────────────────────── -->
  <div class="flex-1 bg-white flex items-center justify-center px-8 overflow-y-auto">
    <div class="w-full max-w-[360px] py-10">

      <!-- Mobile logo -->
      <div class="flex items-center gap-2 mb-8 lg:hidden">
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-white text-xs font-semibold"
             style="background:linear-gradient(135deg,#4F6EF7,#7C3AED)">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
          </svg>
          IPSRS
        </div>
      </div>

      <h2 class="text-2xl font-bold text-gray-900">Daftar Akun Pelapor</h2>
      <p class="mt-1 text-sm text-gray-500">Buat akun untuk melaporkan kerusakan aset RS.</p>

      <?php if (!empty(session()->getFlashdata('error'))): ?>
      <div class="mt-5 flex items-start gap-2.5 px-4 py-3 rounded-xl bg-red-50 border border-red-100">
        <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <p class="text-sm text-red-700"><?= esc(session()->getFlashdata('error')) ?></p>
      </div>
      <?php endif; ?>

      <form method="POST" action="/register" class="mt-8 flex flex-col gap-4">
        <?= csrf_field() ?>

        <!-- Nama Lengkap -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
          <input type="text" name="nama_lengkap" required value="<?= old('nama_lengkap') ?>"
                 placeholder="Nama Lengkap Anda"
                 class="w-full px-4 py-3.5 rounded-2xl bg-gray-50 border-0 focus:ring-2 focus:ring-teal-500/50 text-[14px] text-gray-900 placeholder:text-gray-400 outline-none transition-all shadow-inner">
        </div>

        <!-- Unit Kerja -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit Kerja / Ruangan</label>
          <input type="text" name="unit_kerja" required value="<?= old('unit_kerja') ?>"
                 placeholder="Contoh: IGD, Rawat Inap Melati"
                 class="w-full px-4 py-3.5 rounded-2xl bg-gray-50 border-0 focus:ring-2 focus:ring-teal-500/50 text-[14px] text-gray-900 placeholder:text-gray-400 outline-none transition-all shadow-inner">
        </div>

        <!-- Email -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Alamat Email</label>
          <input type="email" name="email" required autocomplete="email" value="<?= old('email') ?>"
                 placeholder="nama@rsud.go.id"
                 class="w-full px-4 py-3.5 rounded-2xl bg-gray-50 border-0 focus:ring-2 focus:ring-teal-500/50 text-[14px] text-gray-900 placeholder:text-gray-400 outline-none transition-all shadow-inner">
        </div>

        <!-- Password -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
          <div class="relative">
            <input type="password" name="password" id="pw" required autocomplete="new-password"
                   placeholder="••••••••"
                   class="w-full px-4 py-3.5 pr-11 rounded-2xl bg-gray-50 border-0 focus:ring-2 focus:ring-teal-500/50 text-[14px] text-gray-900 placeholder:text-gray-400 outline-none transition-all shadow-inner">
            <button type="button" onclick="togglePw()" tabindex="-1"
                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 transition-colors">
              <svg id="ico-eye" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
              <svg id="ico-eye-off" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Submit -->
        <button type="submit" id="btn-submit"
                class="mt-4 w-full py-3.5 rounded-2xl bg-gradient-to-r from-teal-500 to-emerald-500 hover:shadow-lg hover:shadow-teal-500/30 hover:-translate-y-0.5 text-white text-[15px] font-bold flex items-center justify-center gap-2 transition-all duration-300">
          <svg id="btn-loader" class="hidden w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
          </svg>
          <span id="btn-text">Daftar Sekarang</span>
        </button>
      </form>

      <p class="mt-8 text-center text-xs text-gray-500">
        Sudah punya akun? 
        <a href="/login" class="text-teal-600 font-semibold hover:underline">Masuk di sini</a>
      </p>
    </div>
  </div>

  <script>
    function togglePw() {
      var pw = document.getElementById('pw');
      var eye = document.getElementById('ico-eye');
      var eyeOff = document.getElementById('ico-eye-off');
      if (pw.type === 'password') {
        pw.type = 'text';
        eye.classList.add('hidden');
        eyeOff.classList.remove('hidden');
      } else {
        pw.type = 'password';
        eye.classList.remove('hidden');
        eyeOff.classList.add('hidden');
      }
    }
    document.querySelector('form').addEventListener('submit', function() {
      document.getElementById('btn-loader').classList.remove('hidden');
      document.getElementById('btn-text').textContent = 'Memproses...';
      document.getElementById('btn-submit').disabled = true;
    });
  </script>

</body>
</html>
