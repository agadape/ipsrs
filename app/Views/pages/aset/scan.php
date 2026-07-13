<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>A.I. Tracker — <?= esc($aset['nama'] ?? 'Detail') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    body { font-family: 'Space Grotesk', sans-serif; background: #020617; min-height: 100vh; overflow-x: hidden; color: #e2e8f0; }
    
    /* Cyberpunk Grid Background */
    .cyber-grid {
      position: fixed; inset: 0; z-index: -2;
      background-size: 50px 50px;
      background-image: linear-gradient(to right, rgba(99, 102, 241, 0.05) 1px, transparent 1px),
                        linear-gradient(to bottom, rgba(99, 102, 241, 0.05) 1px, transparent 1px);
      mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
    }

    /* Radar Sweeper */
    .radar-sweep {
      position: fixed; top: -50%; left: -50%; width: 200%; height: 200%; z-index: -1;
      background: conic-gradient(from 0deg, transparent 70%, rgba(99,102,241,0.1) 100%);
      animation: sweep 4s linear infinite; opacity: 0.5;
    }
    @keyframes sweep { to { transform: rotate(360deg); } }

    /* High-tech HUD Card */
    .hud-card {
      background: rgba(15, 23, 42, 0.6);
      backdrop-filter: blur(24px);
      border: 1px solid rgba(99, 102, 241, 0.2);
      box-shadow: 0 0 40px -10px rgba(99, 102, 241, 0.2), inset 0 0 20px rgba(99, 102, 241, 0.05);
      border-radius: 1rem; position: relative;
    }
    .hud-card::before, .hud-card::after {
      content: ''; position: absolute; width: 20px; height: 20px; border: 2px solid #6366f1;
    }
    .hud-card::before { top: -1px; left: -1px; border-right: none; border-bottom: none; border-top-left-radius: 1rem; }
    .hud-card::after { bottom: -1px; right: -1px; border-left: none; border-top: none; border-bottom-right-radius: 1rem; }

    /* Flashing Red Warning UI for Geofence Breach */
    body.breach-mode { background: #450a0a !important; }
    body.breach-mode .cyber-grid { background-image: linear-gradient(to right, rgba(239, 68, 68, 0.1) 1px, transparent 1px), linear-gradient(to bottom, rgba(239, 68, 68, 0.1) 1px, transparent 1px); }
    body.breach-mode .radar-sweep { background: conic-gradient(from 0deg, transparent 70%, rgba(239,68,68,0.2) 100%); }
    body.breach-mode .hud-card { border-color: rgba(239, 68, 68, 0.5); box-shadow: 0 0 50px rgba(239, 68, 68, 0.3), inset 0 0 20px rgba(239, 68, 68, 0.1); }
    body.breach-mode .hud-card::before, body.breach-mode .hud-card::after { border-color: #ef4444; }
    .breach-text { color: #ef4444; text-shadow: 0 0 10px rgba(239, 68, 68, 0.5); }
    .breach-animate { animation: breachFlash 1s ease-in-out infinite; }
    @keyframes breachFlash { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

    /* Map & UI Elements */
    #map-wrapper { transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1); max-height: 0; opacity: 0; overflow: hidden; }
    #map-wrapper.show { max-height: 800px; opacity: 1; margin-top: 1.5rem; }
    
    .btn-cyber {
      background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.5);
      color: #818cf8; text-transform: uppercase; letter-spacing: 2px;
      transition: all 0.3s; position: relative; overflow: hidden;
    }
    .btn-cyber:hover { background: rgba(99, 102, 241, 0.2); box-shadow: 0 0 20px rgba(99, 102, 241, 0.4); }
    .btn-cyber:disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }
    
    .btn-cyber.success { background: rgba(16, 185, 129, 0.1); border-color: #10b981; color: #34d399; box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); }
    .btn-cyber.danger { background: rgba(239, 68, 68, 0.1); border-color: #ef4444; color: #f87171; box-shadow: 0 0 20px rgba(239, 68, 68, 0.4); animation: breachFlash 1s infinite; }

    .telemetry-row { display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.05); padding: 0.5rem 0; font-family: monospace; font-size: 0.75rem; }
    .telemetry-label { color: #64748b; text-transform: uppercase; }
    .telemetry-val { color: #38bdf8; font-weight: bold; }
    body.breach-mode .telemetry-val { color: #f87171; }
  </style>
</head>
<body class="flex flex-col items-center justify-center p-4">

  <div class="cyber-grid"></div>
  <div class="radar-sweep"></div>

  <!-- Header -->
  <div class="w-full max-w-md flex items-center justify-between mb-4 mt-2 px-2">
    <div class="flex items-center gap-2">
      <div id="status-indicator" class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
      <span class="text-indigo-400/80 font-bold tracking-widest text-[10px] uppercase">Aset Telemetry System</span>
    </div>
    <span class="text-slate-500 font-mono text-[10px]" id="clock">00:00:00</span>
  </div>

  <div class="hud-card w-full max-w-md p-6 text-center z-10 transition-colors duration-500">
    
    <!-- Radar Scanner UI -->
    <div id="radar-container" class="relative w-24 h-24 mx-auto mb-6">
      <div id="radar-ring-1" class="absolute inset-0 border-2 border-indigo-500/30 rounded-full"></div>
      <div id="radar-ring-2" class="absolute inset-2 border border-indigo-400/20 rounded-full animate-[spin_3s_linear_infinite] border-t-indigo-400"></div>
      <div class="relative w-full h-full rounded-full flex items-center justify-center bg-indigo-900/20 backdrop-blur-sm">
        <svg id="radar-icon" class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
        </svg>
      </div>
    </div>

    <!-- Data -->
    <h1 class="text-xl font-bold text-white mb-1 uppercase tracking-wider" id="asset-name"><?= esc($aset['nama'] ?? 'Unknown Asset') ?></h1>
    <p class="text-xs font-mono text-indigo-300 mb-6 border border-indigo-500/20 bg-indigo-500/10 inline-block px-3 py-1 rounded">ID: <?= esc($aset['nomor_aset'] ?? 'N/A') ?></p>

    <!-- Map & Advanced Telemetry Container -->
    <div id="map-wrapper" class="text-left w-full">
      <!-- Warning Banner -->
      <div id="breach-warning" class="hidden mb-4 p-3 bg-red-950/50 border border-red-500/50 rounded-lg text-center breach-animate">
        <p class="text-red-400 font-bold text-sm uppercase tracking-widest flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          PELANGGARAN ZONA
        </p>
        <p class="text-red-300/80 text-[10px] mt-1 font-mono">Aset terdeteksi di luar perimeter RSUD Kota Yogyakarta!</p>
      </div>
      
      <!-- Safe Banner -->
      <div id="safe-warning" class="hidden mb-4 p-3 bg-emerald-950/30 border border-emerald-500/30 rounded-lg text-center">
        <p class="text-emerald-400 font-bold text-sm uppercase tracking-widest flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          ZONA AMAN
        </p>
      </div>

      <div class="relative rounded-lg overflow-hidden border border-slate-700 h-48 mb-4">
        <div id="map-container" class="absolute inset-0 z-0"></div>
        <div class="absolute inset-0 pointer-events-none border-[4px] border-indigo-500/20 z-10" id="map-border"></div>
        <!-- Target Reticle Overlay -->
        <div class="absolute inset-0 pointer-events-none flex items-center justify-center z-10 opacity-30">
          <svg class="w-full h-full text-indigo-400" viewBox="0 0 100 100" preserveAspectRatio="none"><path stroke="currentColor" stroke-width="0.5" d="M0,50 L100,50 M50,0 L50,100"/></svg>
        </div>
      </div>

      <!-- Live Telemetry HUD -->
      <div class="bg-slate-900/50 p-3 rounded-lg border border-slate-800 mb-2">
        <div class="telemetry-row">
          <span class="telemetry-label">Reverse Geocoding</span>
          <span class="telemetry-val text-right truncate w-48" id="tlm-address">Scanning...</span>
        </div>
        <div class="telemetry-row">
          <span class="telemetry-label">Jarak dr Pusat RSUD</span>
          <span class="telemetry-val" id="tlm-distance">Menghitung...</span>
        </div>
        <div class="telemetry-row">
          <span class="telemetry-label">Akurasi Satelit</span>
          <span class="telemetry-val" id="tlm-accuracy">0 m</span>
        </div>
        <div class="telemetry-row border-none">
          <span class="telemetry-label">Telemetry Auth</span>
          <span class="telemetry-val">VERIFIED SECURE</span>
        </div>
      </div>
    </div>

    <!-- Action Button -->
    <div class="mt-4">
      <button id="btn-lokasi" onclick="triggerBrutalTracker()" 
              class="btn-cyber w-full py-4 rounded-lg font-bold text-sm flex justify-center items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        <span id="btn-text">INisialisasi Pindai Lokasi</span>
      </button>

      <a href="/ipsrs/aset/<?= esc($aset['id'] ?? '') ?>" class="inline-flex items-center justify-center gap-2 mt-6 text-xs text-slate-500 hover:text-indigo-400 transition-colors uppercase tracking-widest font-bold">
        <span>Buka Panel Master</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    // Live Clock
    setInterval(() => {
      document.getElementById('clock').textContent = new Date().toLocaleTimeString('id-ID');
    }, 1000);

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

    function triggerBrutalTracker() {
      const btn = document.getElementById('btn-lokasi');
      const statusInd = document.getElementById('status-indicator');
      
      btn.disabled = true;
      btn.innerHTML = `<span class="animate-pulse flex items-center gap-2">MENGAKSES SATELIT GPS...</span>`;
      statusInd.className = 'w-2 h-2 rounded-full bg-yellow-400 animate-pulse';

      if (!navigator.geolocation) {
        alert("Sistem Tertolak: Modul GPS tidak ditemukan di perangkat ini.");
        return;
      }

      // Vibrate if supported (Tactile start)
      if(navigator.vibrate) navigator.vibrate(50);

      navigator.geolocation.getCurrentPosition(async function(pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        const acc = pos.coords.accuracy;
        
        btn.innerHTML = `<span class="animate-pulse">MENGUNGGAH TELEMETRY...</span>`;
        
        try {
          const res = await fetch('<?= site_url("ipsrs/aset/" . esc($aset['id'] ?? '')) ?>/ping', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', '<?= csrf_header() ?>': '<?= csrf_hash() ?>' },
            body: JSON.stringify({ lat: lat, lng: lng })
          });
          
          if (!res.ok) throw new Error("Gagal menyimpan ke server RSUD.");

          // Geofencing Logic
          const distance = calcDistance(lat, lng, RSUD_LAT, RSUD_LNG);
          const isBreach = distance > SAFE_RADIUS_METERS;

          // Haptic Feedback Logic
          if(navigator.vibrate) {
            if(isBreach) navigator.vibrate([300, 100, 300, 100, 500]); // Danger pattern
            else navigator.vibrate([100, 50, 100]); // Safe pattern
          }

          // UI Alteration based on Geofence
          document.getElementById('map-wrapper').classList.add('show');
          document.getElementById('tlm-distance').textContent = Math.round(distance) + " Meter";
          document.getElementById('tlm-accuracy').textContent = "±" + Math.round(acc) + " Meter";

          if(isBreach) {
            document.body.classList.add('breach-mode');
            document.getElementById('breach-warning').classList.remove('hidden');
            document.getElementById('map-border').className = "absolute inset-0 pointer-events-none border-[4px] border-red-500/50 z-10 breach-animate";
            statusInd.className = 'w-2 h-2 rounded-full bg-red-500 shadow-[0_0_10px_red]';
            
            btn.className = "btn-cyber danger w-full py-4 rounded-lg font-bold text-sm flex justify-center items-center gap-2";
            btn.innerHTML = `<span>OUT OF BOUNDS DETECTED</span>`;
          } else {
            document.getElementById('safe-warning').classList.remove('hidden');
            statusInd.className = 'w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_10px_#10b981]';
            
            btn.className = "btn-cyber success w-full py-4 rounded-lg font-bold text-sm flex justify-center items-center gap-2";
            btn.innerHTML = `<span>KORDINAT DIAMANKAN</span>`;
          }

          // Leaflet Map Initialization
          if (!mapInstance) {
            setTimeout(() => {
              mapInstance = L.map('map-container', { zoomControl: false, attributionControl: false }).setView([lat, lng], 17);
              // Dark tactical map style
              L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png').addTo(mapInstance);
              
              const dotColor = isBreach ? '#ef4444' : '#10b981';
              const mapDot = L.divIcon({
                className: 'custom-map-dot',
                html: `<div style="width:16px;height:16px;background:${dotColor};border-radius:50%;box-shadow:0 0 20px ${dotColor};border:2px solid white;animation: breachFlash 1s infinite;"></div>`,
                iconSize: [16,16], iconAnchor: [8,8]
              });
              L.marker([lat, lng], {icon: mapDot}).addTo(mapInstance);
            }, 600);
          }

          // Reverse Geocoding via Nominatim
          fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(r => r.json())
            .then(data => {
               document.getElementById('tlm-address').textContent = data.display_name || "Unknown Zone";
            }).catch(() => {
               document.getElementById('tlm-address').textContent = "Satelit Gagal Menerjemahkan Alamat";
            });

        } catch (error) {
          alert("SISTEM ERROR: " + error.message);
          location.reload();
        }

      }, function(err){
        alert("AKSES GPS DITOLAK ATAU SATELIT TIDAK DITEMUKAN. (Kode: " + err.code + ")");
        location.reload();
      }, { timeout: 15000, maximumAge: 0, enableHighAccuracy: true });
    }
  </script>
</body>
</html>
