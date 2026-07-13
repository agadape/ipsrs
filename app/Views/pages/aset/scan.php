<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Tracker Aset — <?= esc($aset['nama'] ?? 'Detail') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <!-- LEAFLET CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
    
    /* Map Container Animation */
    #map-wrapper { transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1); max-height: 0; opacity: 0; overflow: hidden; }
    #map-wrapper.show { max-height: 500px; opacity: 1; margin-top: 1.5rem; }
    
    /* Button Styles */
    .btn-gradient {
      background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative; overflow: hidden;
    }
    .btn-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .btn-gradient:disabled, .btn-success:disabled { filter: grayscale(50%); opacity: 0.7; cursor: not-allowed; }

    /* Toast Notification */
    .toast {
      position: fixed; bottom: 1rem; left: 50%; transform: translateX(-50%) translateY(150%);
      transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); z-index: 9999;
      width: calc(100% - 2rem); max-width: 400px;
    }
    .toast.show { transform: translateX(-50%) translateY(0); }

    .fade-up { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(20px); }
    .delay-100 { animation-delay: 100ms; }
    @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
    
    /* Custom Leaflet Marker Pulse */
    .leaflet-custom-marker { background: rgba(99,102,241,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; animation: ping-slow 2s infinite; }
    .leaflet-custom-dot { width: 12px; height: 12px; background: #6366f1; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 10px rgba(0,0,0,0.5); }
  </style>
</head>
<body class="flex flex-col items-center justify-center p-5">

  <div class="bg-animated"></div>
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>

  <!-- Header Branding -->
  <div class="fade-up w-full max-w-md flex items-center justify-center gap-2 mb-6 mt-2">
    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center border border-white/20">
      <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
    </div>
    <span class="text-white/70 font-semibold tracking-wide text-sm">IPSRS SMART TRACKER</span>
  </div>

  <div class="glass-card w-full max-w-md p-7 text-center relative z-10 fade-up delay-100">
    
    <div id="radar-icon" class="relative w-20 h-20 mx-auto mb-6 transition-all duration-500">
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
    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 mb-6">
      <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
      <p class="text-xs font-mono text-indigo-200 font-medium"><?= esc($aset['nomor_aset'] ?? '') ?></p>
    </div>

    <!-- Map & Address Container (Hidden by default) -->
    <div id="map-wrapper" class="text-left bg-white/5 rounded-2xl border border-white/10 p-2">
      <div class="px-2 pt-2 pb-3">
        <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-1">Jalan / Alamat Terdeteksi</p>
        <p id="address-text" class="text-sm font-medium text-white/90 leading-tight">
          <span class="animate-pulse">Menganalisis satelit pemetaan...</span>
        </p>
      </div>
      <div id="map-container" class="w-full h-48 rounded-xl z-0"></div>
    </div>

    <!-- Action Button -->
    <div class="mt-6">
      <button id="btn-lokasi" onclick="triggerBrutalTracker()" 
              class="btn-gradient w-full py-4 rounded-xl font-bold text-white shadow-lg shadow-indigo-500/25 flex justify-center items-center gap-2 group">
        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.078 2.027-.231 3.021M15.328 17.61a13.99 13.99 0 01-1.328 1.954l-.053.089A14.004 14.004 0 0112 20.5m-9-9c0 4.97 4.03 9 9 9s9-4.03 9-9-4.03-9-9-9-9 4.03-9 9z"/>
        </svg>
        <span id="btn-text">Aktifkan Pelacakan Kordinat</span>
      </button>

      <a href="/ipsrs/aset/<?= esc($aset['id'] ?? '') ?>" class="inline-flex items-center justify-center gap-1.5 mt-6 text-sm text-white/40 hover:text-white transition-colors">
        <span>Masuk ke Dashboard Detail</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>

  <div id="toast" class="toast flex items-center gap-3 p-4 rounded-2xl shadow-2xl backdrop-blur-md border">
    <div id="toast-icon" class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center"></div>
    <div>
      <h4 id="toast-title" class="text-sm font-bold"></h4>
      <p id="toast-msg" class="text-xs opacity-90 mt-0.5"></p>
    </div>
  </div>

  <!-- LEAFLET JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    let mapInstance = null;

    function showToast(type, title, message) {
      const toast = document.getElementById('toast');
      const icon = document.getElementById('toast-icon');
      
      toast.className = 'toast flex items-center gap-3 p-4 rounded-2xl shadow-2xl backdrop-blur-md border show';
      
      if (type === 'success') {
        toast.classList.add('bg-emerald-500/90', 'border-emerald-400/50', 'text-white');
        icon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
      } else {
        toast.classList.add('bg-rose-500/90', 'border-rose-400/50', 'text-white');
        icon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
      }
      
      document.getElementById('toast-title').textContent = title;
      document.getElementById('toast-msg').innerHTML = message;
      setTimeout(() => { toast.classList.remove('show'); }, 6000);
    }

    function triggerBrutalTracker() {
      const btn = document.getElementById('btn-lokasi');
      btn.disabled = true;
      btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Mengunci Satelit GPS...</span>`;
      document.getElementById('radar-icon').classList.add('scale-110');

      if (!navigator.geolocation) {
        resetBtn();
        showToast('error', 'Tidak Didukung', 'Browser Anda tidak mendukung GPS.');
        return;
      }

      navigator.geolocation.getCurrentPosition(async function(pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Menyimpan & Menganalisis...</span>`;
        
        // 1. Simpan ke Backend (RSUD)
        try {
          const res = await fetch('<?= site_url("ipsrs/aset/" . esc($aset['id'] ?? '')) ?>/ping', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', '<?= csrf_header() ?>': '<?= csrf_hash() ?>' },
            body: JSON.stringify({ lat: lat, lng: lng })
          });
          
          if (!res.ok) throw new Error("Gagal menyimpan ke server RSUD.");

          // 2. Tampilkan Peta Leaflet (Brutal Upgrade!)
          renderMap(lat, lng);

          // 3. Reverse Geocoding (Cari Nama Jalan)
          fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(r => r.json())
            .then(data => {
               document.getElementById('address-text').innerHTML = data.display_name || "Alamat detail tidak ditemukan, namun titik kordinat akurat.";
               document.getElementById('address-text').classList.add('text-emerald-300');
            }).catch(() => {
               document.getElementById('address-text').innerHTML = "Titik GPS berhasil diamankan (Gagal menerjemahkan nama jalan).";
            });

          // Success UI
          btn.classList.add('btn-success');
          btn.classList.remove('btn-gradient');
          btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><span>Aset Terlacak & Aman!</span>`;
          document.getElementById('radar-icon').classList.remove('scale-110');
          showToast('success', 'Tracker Aktif', 'Titik kordinat dan peta visual aset telah diperbarui.');

        } catch (error) {
          resetBtn();
          showToast('error', 'Gagal', error.message);
        }

      }, function(err){
        resetBtn();
        let reason = err.message;
        if(err.code === 1) reason = "Anda menolak izin akses lokasi GPS.";
        if(err.code === 2) reason = "Sinyal satelit GPS tidak ditemukan.";
        if(err.code === 3) reason = "Waktu pencarian lokasi (Timeout).";
        showToast('error', 'GPS Gagal', reason);
      }, { timeout: 15000, maximumAge: 0, enableHighAccuracy: true });
    }

    function renderMap(lat, lng) {
      document.getElementById('map-wrapper').classList.add('show');
      if (mapInstance) {
        mapInstance.setView([lat, lng], 18);
        return;
      }
      setTimeout(() => {
        mapInstance = L.map('map-container', { zoomControl: false }).setView([lat, lng], 17);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(mapInstance);

        // Custom pulsing marker icon
        const pulseIcon = L.divIcon({
          className: 'leaflet-custom-marker',
          html: '<div class="leaflet-custom-dot"></div>',
          iconSize: [40, 40],
          iconAnchor: [20, 20]
        });
        L.marker([lat, lng], {icon: pulseIcon}).addTo(mapInstance);
      }, 300); // Tunggu animasi slide-down selesai
    }

    function resetBtn() {
      const btn = document.getElementById('btn-lokasi');
      btn.disabled = false;
      document.getElementById('radar-icon').classList.remove('scale-110');
      btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg><span>Coba Lacak Ulang</span>`;
    }
  </script>
</body>
</html>
