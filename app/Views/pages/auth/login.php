<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Masuk — IPSRS RSUD Kota Yogyakarta</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-slate-100 selection:bg-indigo-100 selection:text-indigo-900 antialiased"
      style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 32px 32px;">

  <!-- Main Floating Container -->
  <div class="w-full max-w-[1000px] bg-white rounded-[24px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] flex flex-col md:flex-row overflow-hidden border border-slate-200/60 relative z-10">

    <!-- ── Left Brand Panel ────────────────────────────────────────────── -->
    <div class="hidden md:flex flex-col justify-between w-5/12 bg-slate-900 p-10 relative overflow-hidden">
      <!-- Subtle Grid Pattern -->
      <div class="absolute inset-0 opacity-[0.03]" 
           style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 24px 24px;">
      </div>
      
      <!-- Top: Logo -->
      <div class="relative z-10 flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
          </svg>
        </div>
        <span class="text-white font-bold tracking-wide">IPSRS V2</span>
      </div>

      <!-- Middle: Typography Statement -->
      <div class="relative z-10 my-16">
        <h1 class="text-3xl font-bold text-white leading-[1.2] tracking-tight mb-4">
          Manajemen<br>
          Pemeliharaan<br>
          <span class="text-indigo-400">Aset & Sarana.</span>
        </h1>
        <p class="text-slate-400 text-sm leading-relaxed max-w-[240px]">
          Platform terpadu untuk monitoring kerusakan, penjadwalan preventif, dan kontrol suku cadang.
        </p>
      </div>

      <!-- Bottom: Footer Info -->
      <div class="relative z-10 border-t border-slate-800 pt-6">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center border border-slate-700">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          </div>
          <div>
            <p class="text-white text-xs font-bold tracking-wide">RSUD Kota Yogyakarta</p>
            <p class="text-slate-400 text-[11px] font-medium mt-0.5">Sistem Internal Enterprise</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Right Form Panel ────────────────────────────────────────────── -->
    <div class="w-full md:w-7/12 p-8 md:p-14 flex flex-col justify-center bg-white relative">
      
      <!-- Mobile Logo (Visible only on small screens) -->
      <div class="md:hidden flex items-center gap-2 mb-8">
        <div class="w-8 h-8 rounded-md bg-indigo-600 flex items-center justify-center text-white shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
          </svg>
        </div>
        <span class="text-slate-900 font-bold tracking-wide">IPSRS V2</span>
      </div>

      <div class="max-w-[360px] w-full mx-auto">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight mb-2">Masuk ke Akun</h2>
        <p class="text-sm text-slate-500 mb-8">Masukkan email dan kata sandi Anda untuk melanjutkan.</p>
        
        <?php if (!empty(session()->getFlashdata('error'))): ?>
        <div class="mb-6 flex items-start gap-2.5 px-3 py-2.5 rounded-lg bg-red-50 border border-red-100">
          <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <p class="text-[13px] font-medium text-red-800 leading-relaxed"><?= esc(session()->getFlashdata('error')) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty(session()->getFlashdata('success'))): ?>
        <div class="mb-6 flex items-start gap-2.5 px-3 py-2.5 rounded-lg bg-emerald-50 border border-emerald-100">
          <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-[13px] font-medium text-emerald-800 leading-relaxed"><?= esc(session()->getFlashdata('success')) ?></p>
        </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="flex flex-col gap-4">
          <?= csrf_field() ?>

          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Email</label>
            <input type="email" name="email" required autocomplete="email"
                   placeholder="nama@rsud.go.id"
                   class="w-full px-3.5 py-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-[13px] text-slate-900 placeholder:text-slate-400 outline-none transition-all">
          </div>

          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kata Sandi</label>
              <a href="#" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 hover:underline" tabindex="-1">Lupa sandi?</a>
            </div>
            <div class="relative">
              <input type="password" name="password" id="pw" required autocomplete="current-password"
                     placeholder="••••••••"
                     class="w-full px-3.5 py-2.5 pr-10 rounded-lg bg-slate-50 border border-slate-200 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-[13px] text-slate-900 placeholder:text-slate-400 outline-none transition-all">
              <button type="button" onclick="togglePw()" tabindex="-1"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                <svg id="ico-eye" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                <svg id="ico-eye-off" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
          </div>

          <button type="submit" id="btn-submit"
                  class="mt-4 w-full py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-[13px] font-semibold flex items-center justify-center gap-2 transition-all shadow-sm">
            <svg id="btn-loader" class="hidden w-4 h-4 animate-spin text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <span id="btn-text">Masuk Sekarang</span>
          </button>
        </form>

        <p class="mt-8 text-center text-[13px] text-slate-500 font-medium">
          Belum memiliki akun? 
          <a href="/register" class="text-indigo-600 font-semibold hover:text-indigo-700 hover:underline ml-1">Daftar sebagai Pelapor</a>
        </p>
      </div>

    </div>
  </div>

  <script>
    function togglePw() {
      const pw = document.getElementById('pw');
      const eye = document.getElementById('ico-eye');
      const eyeOff = document.getElementById('ico-eye-off');
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
      document.getElementById('btn-text').textContent = 'Memverifikasi...';
      const btn = document.getElementById('btn-submit');
      btn.disabled = true;
      btn.classList.add('opacity-80', 'cursor-not-allowed');
    });
  </script>
</body>
</html>
