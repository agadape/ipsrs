<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Scan Aset — <?= esc($aset['nama'] ?? 'Detail') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); min-height: 100vh; }
    .card { background: rgba(255,255,255,0.9); backdrop-filter: blur(16px); border-radius: 1.5rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.7); }
    .btn-primary { background: linear-gradient(to right, #4f46e5, #7c3aed); color: white; transition: all 0.3s; }
    .btn-primary:active { transform: scale(0.97); }
    .btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
  </style>
</head>
<body class="flex items-center justify-center p-4">

  <div class="card w-full max-w-md p-8 text-center relative overflow-hidden">
    <!-- Decorative background blobs -->
    <div class="absolute -top-16 -right-16 w-32 h-32 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
    <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>

    <div class="relative z-10">
      <!-- Icon -->
      <div class="w-20 h-20 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
        <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
      </div>

      <h1 class="text-2xl font-bold text-gray-800 mb-1"><?= esc($aset['nama'] ?? 'Detail Aset') ?></h1>
      <p class="text-sm font-mono text-indigo-600 font-semibold mb-6"><?= esc($aset['nomor_aset'] ?? '') ?></p>

      <div class="bg-gray-50 rounded-xl p-4 mb-8 text-left border border-gray-100">
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <p class="text-gray-400 text-xs uppercase tracking-wider font-bold mb-1">Kategori</p>
            <p class="font-medium text-gray-700"><?= esc($aset['kategori'] ?? '-') ?></p>
          </div>
          <div>
            <p class="text-gray-400 text-xs uppercase tracking-wider font-bold mb-1">Kondisi</p>
            <p class="font-medium text-gray-700"><?= esc($aset['kondisi'] ?? '-') ?></p>
          </div>
          <div class="col-span-2 border-t border-gray-200 pt-3 mt-1">
            <p class="text-gray-400 text-xs uppercase tracking-wider font-bold mb-1">Lokasi Saat Ini</p>
            <p class="font-medium text-gray-700"><?= esc($aset['lokasi'] ?? '-') ?> (<?= esc($aset['ruangan'] ?? '-') ?>)</p>
          </div>
        </div>
      </div>

      <div id="status-box" class="mb-6 hidden rounded-xl p-4 text-sm font-medium border"></div>

      <button id="btn-lokasi" onclick="requestLocation()" 
              class="btn-primary w-full py-3.5 rounded-xl font-bold text-base shadow-lg shadow-indigo-500/30 flex justify-center items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>Update Lokasi Aset Ini</span>
      </button>

      <a href="/ipsrs/aset/<?= esc($aset['id'] ?? '') ?>" class="block mt-6 text-sm text-gray-500 hover:text-indigo-600 font-medium transition-colors">
        Lihat Detail Lengkap &rarr;
      </a>
    </div>
  </div>

  <script>
    function requestLocation() {
      var btn = document.getElementById('btn-lokasi');
      var statusBox = document.getElementById('status-box');
      
      btn.disabled = true;
      btn.innerHTML = `
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Mencari Lokasi GPS...
      `;
      statusBox.classList.add('hidden');

      if (!navigator.geolocation) {
        showError("Browser HP Anda tidak mendukung deteksi lokasi (GPS).");
        return;
      }

      navigator.geolocation.getCurrentPosition(function(pos) {
        btn.innerHTML = "Menyimpan ke Database...";
        
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
            btn.classList.add('hidden');
            statusBox.className = "mb-6 rounded-xl p-4 text-sm font-medium border bg-emerald-50 border-emerald-200 text-emerald-700";
            statusBox.innerHTML = "✅ Lokasi aset berhasil diperbarui!";
          } else {
            r.json().then(data => showError("Gagal menyimpan lokasi: " + (data.msg || r.statusText))).catch(() => showError("Error " + r.status));
          }
        }).catch(function(err){
          showError("Gagal menghubungi server: " + err.message);
        });
      }, function(err){
        var reason = err.message;
        if(err.code === 1) reason = "Izin lokasi ditolak oleh Anda.";
        if(err.code === 2) reason = "Sinyal GPS tidak tersedia.";
        if(err.code === 3) reason = "Waktu pencarian habis (Timeout).";
        
        showError("Gagal membaca GPS: " + reason + "<br><br>Pastikan izin Lokasi/GPS menyala saat scan.");
      }, { timeout: 15000, maximumAge: 0, enableHighAccuracy: true });
    }

    function showError(msg) {
      var btn = document.getElementById('btn-lokasi');
      var statusBox = document.getElementById('status-box');
      
      btn.disabled = false;
      btn.innerHTML = `
        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Coba Lagi
      `;
      
      statusBox.classList.remove('hidden');
      statusBox.className = "mb-6 rounded-xl p-4 text-sm border bg-red-50 border-red-200 text-red-700 text-left";
      statusBox.innerHTML = "❌ " + msg;
    }
  </script>
</body>
</html>
