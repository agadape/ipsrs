<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Verifikasi Lokasi — <?= esc($aset['nama'] ?? 'Detail Aset') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    body { font-family: 'Public Sans', sans-serif; background-color: #f8f9fa; color: #435971; }
    .card { background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 0.25rem 1.125rem rgba(75, 70, 92, 0.1); }
    
    .btn-primary { background-color: #696cff; color: #fff; border-color: #696cff; box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4); transition: all 0.2s ease-in-out; }
    .btn-primary:hover { background-color: #5f61e6; border-color: #5f61e6; transform: translateY(-1px); }
    .btn-primary:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }
    
    .btn-success { background-color: #71dd37; color: #fff; border-color: #71dd37; box-shadow: 0 0.125rem 0.25rem 0 rgba(113, 221, 55, 0.4); }
    .btn-danger { background-color: #ff3e1d; color: #fff; border-color: #ff3e1d; box-shadow: 0 0.125rem 0.25rem 0 rgba(255, 62, 29, 0.4); }

    .badge-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; color: #a1acb8; margin-bottom: 0.25rem; }
    .data-value { font-weight: 600; color: #566a7f; font-size: 0.875rem; }

    #map-wrapper { transition: all 0.5s ease-in-out; max-height: 0; opacity: 0; overflow: hidden; }
    #map-wrapper.show { max-height: 800px; opacity: 1; margin-top: 1rem; }

    .custom-map-dot { border-radius: 50%; box-shadow: 0 0 10px rgba(0,0,0,0.3); border: 2px solid white; }
  </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-4">

  <!-- Logo & Header -->
  <div class="w-full max-w-md flex flex-col items-center mb-6 mt-4">
    <div class="w-12 h-12 rounded-lg bg-[#696cff]/10 flex items-center justify-center mb-3">
      <svg class="w-7 h-7 text-[#696cff]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
    </div>
    <h2 class="text-xl font-bold text-[#566a7f]">IPSRS RSUD JOGJA</h2>
    <p class="text-sm text-[#a1acb8]">Sistem Verifikasi Lokasi Aset</p>
  </div>

  <div class="card w-full max-w-md p-6 relative z-10">
    
    <!-- Asset Info -->
    <div class="text-center mb-6 pb-6 border-b border-gray-100">
      <h1 class="text-2xl font-bold text-[#566a7f] mb-1"><?= esc($aset['nama'] ?? 'Detail Aset') ?></h1>
      <span class="inline-block px-3 py-1 rounded-full bg-gray-100 text-[#566a7f] text-xs font-semibold">
        ID: <?= esc($aset['nomor_aset'] ?? '-') ?>
      </span>
    </div>

    <!-- Data Grid -->
    <div class="grid grid-cols-2 gap-4 text-left mb-6">
      <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
        <p class="badge-label">Kategori</p>
        <p class="data-value"><?= esc($aset['kategori'] ?? '-') ?></p>
      </div>
      <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
        <p class="badge-label">Kondisi</p>
        <div class="flex items-center gap-1.5">
          <?php $isBaik = strtolower($aset['kondisi'] ?? '') === 'baik'; ?>
          <span class="h-2.5 w-2.5 rounded-full <?= $isBaik ? 'bg-green-500' : 'bg-yellow-500' ?>"></span>
          <p class="data-value"><?= esc($aset['kondisi'] ?? '-') ?></p>
        </div>
      </div>
      <div class="col-span-2 bg-slate-50 rounded-lg p-3 border border-slate-100">
        <p class="badge-label">Lokasi Saat Ini (Sistem)</p>
        <p class="data-value"><?= esc($aset['lokasi'] ?? '-') ?> <span class="text-gray-400 font-normal">/ <?= esc($aset['ruangan'] ?? '-') ?></span></p>
      </div>
    </div>

    <!-- Map & Advanced Telemetry Container -->
    <div id="map-wrapper" class="w-full mb-6">
      <!-- Warning Banner -->
      <div id="breach-warning" class="hidden mb-3 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
          <h4 class="text-red-700 font-bold text-sm">Peringatan: Di Luar Area RSUD</h4>
          <p class="text-red-600 text-xs mt-0.5">Aset terdeteksi berada jauh dari kawasan Rumah Sakit.</p>
        </div>
      </div>
      
      <!-- Safe Banner -->
      <div id="safe-warning" class="hidden mb-3 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <h4 class="text-green-700 font-bold text-sm">Aset berada di dalam kawasan RSUD.</h4>
      </div>

      <div class="relative rounded-lg overflow-hidden border border-gray-200 h-48 mb-3 z-0">
        <div id="map-container" class="absolute inset-0"></div>
      </div>

      <!-- Live Telemetry -->
      <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 space-y-2">
        <div class="flex justify-between items-start border-b border-gray-200 pb-2">
          <span class="text-xs text-gray-500 font-semibold w-1/3">Alamat Deteksi</span>
          <span class="text-xs text-[#566a7f] font-medium text-right w-2/3" id="tlm-address">Scanning...</span>
        </div>
        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
          <span class="text-xs text-gray-500 font-semibold">Jarak dari RS</span>
          <span class="text-xs text-[#566a7f] font-medium" id="tlm-distance">Menghitung...</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-xs text-gray-500 font-semibold">Akurasi GPS</span>
          <span class="text-xs text-[#566a7f] font-medium" id="tlm-accuracy">0 m</span>
        </div>
      </div>
    </div>

    <!-- Action Button -->
    <div class="mt-2">
      <button id="btn-lokasi" onclick="triggerLocationScan()" 
              class="btn-primary w-full py-3 rounded-lg font-bold text-sm flex justify-center items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span id="btn-text">Verifikasi Posisi Saat Ini</span>
      </button>

      <div class="text-center mt-4">
        <a href="/ipsrs/aset/<?= esc($aset['id'] ?? '') ?>" class="text-sm text-[#696cff] hover:text-[#5f61e6] font-medium inline-flex items-center gap-1 transition-colors">
          Kembali ke Detail Aset
        </a>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    // Geofencing Constants (Kordinat RSUD Kota Yogyakarta)
    const RSUD_LAT = -7.8256;
    const RSUD_LNG = 110.3780;
    const SAFE_RADIUS_METERS = 500; // 500 meter dari pusat RSUD
    let mapInstance = null;

    // Haversine Formula for precise distance
    function calcDistance(lat1, lon1, lat2, lon2) {
      const R = 6371e3; // metres
      const p1 = lat1 * Math.PI/180;
      const p2 = lat2 * Math.PI/180;
      const dp = (lat2-lat1) * Math.PI/180;
      const dl = (lon2-lon1) * Math.PI/180;
      const a = Math.sin(dp/2) * Math.sin(dp/2) + Math.cos(p1) * Math.cos(p2) * Math.sin(dl/2) * Math.sin(dl/2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      return R * c; // in metres
    }

    function triggerLocationScan() {
      const btn = document.getElementById('btn-lokasi');
      
      btn.disabled = true;
      btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Mengakses GPS...</span>`;

      if (!navigator.geolocation) {
        alert("Modul GPS tidak ditemukan di browser Anda.");
        resetBtn();
        return;
      }

      // Vibrate if supported
      if(navigator.vibrate) navigator.vibrate(50);

      navigator.geolocation.getCurrentPosition(async function(pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        const acc = pos.coords.accuracy;
        
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Menyimpan Data...</span>`;
        
        try {
          const res = await fetch('<?= site_url("ipsrs/aset/" . esc($aset['id'] ?? '')) ?>/ping', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', '<?= csrf_header() ?>': '<?= csrf_hash() ?>' },
            body: JSON.stringify({ lat: lat, lng: lng })
          });
          
          if (!res.ok) throw new Error("Gagal menyimpan lokasi ke server.");

          // Geofencing Logic
          const distance = calcDistance(lat, lng, RSUD_LAT, RSUD_LNG);
          const isBreach = distance > SAFE_RADIUS_METERS;

          // Haptic Feedback Logic
          if(navigator.vibrate) {
            if(isBreach) navigator.vibrate([300, 100, 300, 100, 500]); // Danger pattern
            else navigator.vibrate([100, 50, 100]); // Safe pattern
          }

          // UI Update
          document.getElementById('map-wrapper').classList.add('show');
          document.getElementById('tlm-distance').textContent = Math.round(distance) + " Meter";
          document.getElementById('tlm-accuracy').textContent = "± " + Math.round(acc) + " Meter";

          if(isBreach) {
            document.getElementById('breach-warning').classList.remove('hidden');
            document.getElementById('safe-warning').classList.add('hidden');
            btn.className = "btn-danger w-full py-3 rounded-lg font-bold text-sm flex justify-center items-center gap-2";
            btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><span>Tersimpan (Luar Zona)</span>`;
          } else {
            document.getElementById('safe-warning').classList.remove('hidden');
            document.getElementById('breach-warning').classList.add('hidden');
            btn.className = "btn-success w-full py-3 rounded-lg font-bold text-sm flex justify-center items-center gap-2";
            btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Lokasi Diverifikasi</span>`;
          }

          // Leaflet Map Initialization
          if (!mapInstance) {
            setTimeout(() => {
              mapInstance = L.map('map-container', { zoomControl: false, attributionControl: false }).setView([lat, lng], 16);
              // Clean map style
              L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapInstance);
              
              const dotColor = isBreach ? '#ff3e1d' : '#71dd37';
              const mapDot = L.divIcon({
                className: 'custom-map-dot',
                html: `<div style="width:16px;height:16px;background:${dotColor};border-radius:50%;"></div>`,
                iconSize: [16,16], iconAnchor: [8,8]
              });
              L.marker([lat, lng], {icon: mapDot}).addTo(mapInstance);
            }, 500);
          }

          // Reverse Geocoding via Nominatim
          fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(r => r.json())
            .then(data => {
               document.getElementById('tlm-address').textContent = data.display_name || "Detail alamat tidak ditemukan";
            }).catch(() => {
               document.getElementById('tlm-address').textContent = "Gagal menerjemahkan nama jalan";
            });

        } catch (error) {
          alert("SISTEM ERROR: " + error.message);
          resetBtn();
        }

      }, function(err){
        if(err.code === 1) {
          document.getElementById('ios-permission-modal').classList.remove('hidden');
        } else {
          let reason = "Gagal mengambil lokasi.";
          if(err.code === 2) reason = "Posisi GPS tidak tersedia.";
          if(err.code === 3) reason = "Waktu pencarian lokasi habis.";
          alert(reason);
        }
        resetBtn();
      }, { timeout: 15000, maximumAge: 0, enableHighAccuracy: true });
    }

    function resetBtn() {
      const btn = document.getElementById('btn-lokasi');
      btn.disabled = false;
      btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>Coba Verifikasi Ulang</span>`;
    }

    function closeIosModal() {
      document.getElementById('ios-permission-modal').classList.add('hidden');
    }
  </script>

  <!-- iOS Permission Guide Modal -->
  <div id="ios-permission-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl relative overflow-hidden animate-[bounce_0.5s_ease-out]">
      <!-- Top decorative gradient -->
      <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-red-500 to-orange-400"></div>
      
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900">Akses Lokasi Ditolak</h3>
        </div>
      </div>
      
      <div class="text-sm text-gray-600 space-y-3 mb-6 leading-relaxed">
        <p>Browser Anda memblokir fitur GPS. Aplikasi ini <b>wajib</b> mengetahui lokasi Anda untuk memverifikasi posisi aset secara *real-time*.</p>
        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
          <p class="font-bold text-gray-800 mb-1">Jika Anda menggunakan iPhone (iOS):</p>
          <ol class="list-decimal pl-5 space-y-1 text-xs text-gray-600">
            <li>Buka <b>Pengaturan</b> (Settings) HP Anda</li>
            <li>Pilih <b>Privasi & Keamanan</b></li>
            <li>Pilih <b>Layanan Lokasi</b></li>
            <li>Cari browser Anda (<b>Safari</b> / Chrome)</li>
            <li>Ubah izin menjadi <b>"Izinkan"</b> (Allow)</li>
            <li><b>Refresh/Muat Ulang</b> halaman ini</li>
          </ol>
        </div>
        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
          <p class="font-bold text-gray-800 mb-1">Jika Anda menggunakan Android:</p>
          <p class="text-xs text-gray-600">Klik icon <b>gembok 🔒</b> di pojok kiri atas baris URL browser, pilih <b>Izin (Permissions)</b>, dan aktifkan <b>Lokasi</b>.</p>
        </div>
      </div>
      
      <button type="button" onclick="closeIosModal()" class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition-colors">
        Saya Mengerti
      </button>
    </div>
  </div>
</body>
</html>
