<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Scan Aset — <?= esc($aset['nama'] ?? 'Detail') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; min-height: 100vh; overflow-x: hidden; }
    /* Animated Gradient Background */
    .bg-animated {
      position: fixed; inset: 0; z-index: -1;
      background: radial-gradient(circle at top left, #312e81 0%, #0f172a 50%), radial-gradient(circle at bottom right, #1e1b4b 0%, #0f172a 50%);
    }
    .blob {
      position: absolute; filter: blur(60px); opacity: 0.6; z-index: -1;
      animation: float 10s ease-in-out infinite;
    }
    .blob-1 { top: -10%; left: -10%; width: 300px; height: 300px; background: #6366f1; }
    .blob-2 { bottom: -10%; right: -10%; width: 300px; height: 300px; background: #a855f7; animation-delay: -5s; }
    
    @keyframes float {
      0%, 100% { transform: translate(0, 0) scale(1); }
      25% { transform: translate(5%, 10%) scale(1.1); }
      50% { transform: translate(10%, -5%) scale(0.9); }
      75% { transform: translate(-5%, 15%) scale(1.05); }
    }

    /* Glassmorphism Card */
    .glass-card {
      background: rgba(255, 255, 255, 0.03);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      border-radius: 1.5rem;
    }

    /* Pulse Map Pin Animation */
    @keyframes ping-slow {
      75%, 100% { transform: scale(2); opacity: 0; }
    }
    .animate-ping-slow { animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite; }
    
    /* Button Styles */
    .btn-gradient {
      background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative; overflow: hidden;
    }
    .btn-gradient::after {
      content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
      opacity: 0; transition: opacity 0.3s;
    }
    .btn-gradient:active { transform: scale(0.97); }
    .btn-gradient:disabled { filter: grayscale(50%); opacity: 0.7; cursor: not-allowed; transform: none; }
    .btn-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

    /* Toast Notification */
    .toast {
      position: fixed; bottom: 1rem; left: 50%; transform: translateX(-50%) translateY(150%);
      transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); z-index: 50;
      width: calc(100% - 2rem); max-width: 400px;
    }
    .toast.show { transform: translateX(-50%) translateY(0); }

    /* Entrance Animation */
    .fade-up { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(20px); }
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-300 { animation-delay: 300ms; }
    @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body class="flex flex-col items-center justify-center p-5">

  <div class="bg-animated"></div>
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>

  <!-- Header Branding -->
  <div class="fade-up w-full max-w-md flex items-center justify-center gap-2 mb-8 mt-4">
    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center border border-white/20">
      <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
    </div>
    <span class="text-white/70 font-semibold tracking-wide text-sm">IPSRS RSUD JOGJA</span>
  </div>

  <div class="glass-card w-full max-w-md p-7 text-center relative z-10 fade-up delay-100">
    
    <!-- Radar Icon -->
    <div class="relative w-20 h-20 mx-auto mb-6">
      <div class="absolute inset-0 bg-indigo-500/20 rounded-full animate-ping-slow"></div>
      <div class="absolute inset-2 bg-indigo-500/30 rounded-full animate-ping-slow" style="animation-delay: 0.5s"></div>
      <div class="relative w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg border border-white/20">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </div>
    </div>

    <h1 class="text-2xl font-bold text-white mb-1 tracking-tight"><?= esc($aset['nama'] ?? 'Detail Aset') ?></h1>
    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 mb-8">
      <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
      <p class="text-xs font-mono text-indigo-200 font-medium"><?= esc($aset['nomor_aset'] ?? '') ?></p>
    </div>

    <!-- Data Grid -->
    <div class="grid grid-cols-2 gap-4 text-left mb-8 fade-up delay-200">
      <div class="bg-white/5 rounded-xl p-3.5 border border-white/5">
        <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-1">Kategori</p>
        <p class="font-medium text-white/90 text-sm"><?= esc($aset['kategori'] ?? '-') ?></p>
      </div>
      <div class="bg-white/5 rounded-xl p-3.5 border border-white/5">
        <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-1">Kondisi</p>
        <div class="flex items-center gap-1.5">
          <?php $isBaik = strtolower($aset['kondisi'] ?? '') === 'baik'; ?>
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 <?= $isBaik ? 'bg-emerald-400' : 'bg-amber-400' ?>"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 <?= $isBaik ? 'bg-emerald-500' : 'bg-amber-500' ?>"></span>
          </span>
          <p class="font-medium text-white/90 text-sm"><?= esc($aset['kondisi'] ?? '-') ?></p>
        </div>
      </div>
      <div class="col-span-2 bg-white/5 rounded-xl p-4 border border-white/5 relative overflow-hidden group">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] [mask-image:linear-gradient(to_bottom,white,transparent)] opacity-50"></div>
        <div class="relative z-10">
          <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-1">Lokasi Saat Ini</p>
          <p class="font-semibold text-white text-base leading-tight"><?= esc($aset['lokasi'] ?? '-') ?></p>
          <p class="text-sm text-indigo-200/70 mt-0.5"><?= esc($aset['ruangan'] ?? '-') ?></p>
        </div>
      </div>
    </div>

    <!-- Action Button -->
    <div class="fade-up delay-300">
      <button id="btn-lokasi" onclick="requestLocation()" 
              class="btn-gradient w-full py-4 rounded-xl font-bold text-white shadow-lg shadow-indigo-500/25 flex justify-center items-center gap-2 group">
        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.078 2.027-.231 3.021M15.328 17.61a13.99 13.99 0 01-1.328 1.954l-.053.089A14.004 14.004 0 0112 20.5m-9-9c0 4.97 4.03 9 9 9s9-4.03 9-9-4.03-9-9-9-9 4.03-9 9z"/>
        </svg>
        <span id="btn-text">Verifikasi Posisi Aset</span>
      </button>

      <a href="/ipsrs/aset/<?= esc($aset['id'] ?? '') ?>" class="inline-flex items-center justify-center gap-1.5 mt-6 text-sm text-white/40 hover:text-white transition-colors">
        <span>Lihat Detail Lengkap</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>

  <!-- Beautiful Toast Notification -->
  <div id="toast" class="toast flex items-center gap-3 p-4 rounded-2xl shadow-2xl backdrop-blur-md border">
    <div id="toast-icon" class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center"></div>
    <div>
      <h4 id="toast-title" class="text-sm font-bold"></h4>
      <p id="toast-msg" class="text-xs opacity-90 mt-0.5"></p>
    </div>
  </div>

  <script>
    function showToast(type, title, message) {
      const toast = document.getElementById('toast');
      const icon = document.getElementById('toast-icon');
      const tTitle = document.getElementById('toast-title');
      const tMsg = document.getElementById('toast-msg');
      
      toast.className = 'toast flex items-center gap-3 p-4 rounded-2xl shadow-2xl backdrop-blur-md border show';
      
      if (type === 'success') {
        toast.classList.add('bg-emerald-500/90', 'border-emerald-400/50', 'text-white');
        icon.className = 'shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center';
        icon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
      } else {
        toast.classList.add('bg-rose-500/90', 'border-rose-400/50', 'text-white');
        icon.className = 'shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center';
        icon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
      }
      
      tTitle.textContent = title;
      tMsg.innerHTML = message;

      setTimeout(() => { toast.classList.remove('show'); }, 5000);
    }

    function requestLocation() {
      const btn = document.getElementById('btn-lokasi');
      const btnText = document.getElementById('btn-text');
      
      // Loading State
      btn.disabled = true;
      btn.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Mengunci Satelit...</span>
      `;

      if (!navigator.geolocation) {
        resetBtn();
        showToast('error', 'Tidak Didukung', 'Browser HP Anda tidak mendukung deteksi lokasi (GPS).');
        return;
      }

      navigator.geolocation.getCurrentPosition(function(pos) {
        btn.innerHTML = `
          <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
          <span>Sinkronisasi...</span>
        `;
        
        fetch('<?= site_url("ipsrs/aset/" . esc($aset['id'] ?? '')) ?>/ping', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json', 
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
          },
          body: JSON.stringify({ lat: pos.coords.latitude, lng: pos.coords.longitude })
        }).then(function(r) {
          if (r.ok) {
            // Success State
            btn.classList.add('btn-success');
            btn.classList.remove('btn-gradient');
            btn.innerHTML = `
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              <span>Lokasi Terverifikasi!</span>
            `;
            showToast('success', 'Berhasil', 'Titik kordinat aset telah diperbarui ke database RSUD.');
          } else {
            resetBtn();
            r.json().then(data => showToast('error', 'Gagal Simpan', data.msg || r.statusText)).catch(() => showToast('error', 'Server Error', 'Status: ' + r.status));
          }
        }).catch(function(err){
          resetBtn();
          showToast('error', 'Koneksi Terputus', 'Gagal menghubungi server: ' + err.message);
        });
      }, function(err){
        resetBtn();
        let reason = err.message;
        if(err.code === 1) reason = "Anda menolak izin akses lokasi GPS.";
        if(err.code === 2) reason = "Sinyal satelit GPS tidak ditemukan.";
        if(err.code === 3) reason = "Waktu pencarian lokasi (Timeout).";
        showToast('error', 'GPS Gagal', reason + '<br>Pastikan Location/GPS HP Anda aktif.');
      }, { timeout: 15000, maximumAge: 0, enableHighAccuracy: true });
    }

    function resetBtn() {
      const btn = document.getElementById('btn-lokasi');
      btn.disabled = false;
      btn.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span>Coba Verifikasi Ulang</span>
      `;
    }
  </script>
</body>
</html>
