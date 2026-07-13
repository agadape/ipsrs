<?php
$id = $aset['id'] ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div class="flex items-center gap-3">
    <a href="/ipsrs/aset"
       class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 hover:border-gray-200 transition-colors text-gray-500 hover:text-gray-700">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </a>
    <div>
      <h1 class="text-xl font-bold text-gray-800"><?= esc($aset['nama'] ?? 'Detail Aset') ?></h1>
      <p class="text-xs font-mono text-indigo-600 mt-0.5"><?= esc($aset['nomor_aset'] ?? $id) ?></p>
    </div>
  </div>
  <div class="flex items-center gap-2">
    <a href="/ipsrs/aset/<?= esc($id) ?>/qr"
       class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm border border-gray-200 hover:shadow-md">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4z"/>
      </svg>
      QR Code
    </a>
    <a href="/ipsrs/aset/<?= esc($id) ?>/edit"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
      </svg>
      Edit Aset
    </a>
  </div>
</div>

<!-- Info Grid -->
<div class="card p-6 mb-6">
  <h2 class="text-sm font-semibold text-gray-700 mb-5 pb-3 border-b border-gray-100">Informasi Aset</h2>
  <div class="grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-5">
    <?php
    $fields = [
      ['label' => 'Nomor Aset', 'value' => $aset['nomor_aset'] ?? '-', 'mono' => true],
      ['label' => 'Nama',       'value' => $aset['nama'] ?? '-'],
      ['label' => 'Jenis',      'value' => $aset['jenis'] ?? '-'],
      ['label' => 'Kategori',   'value' => $aset['kategori'] ?? '-'],
      ['label' => 'Lokasi',     'value' => $aset['lokasi'] ?? '-'],
      ['label' => 'Gedung',     'value' => $aset['gedung'] ?? '-'],
      ['label' => 'Lantai',     'value' => $aset['lantai'] ?? '-'],
      ['label' => 'Ruangan',    'value' => $aset['ruangan'] ?? '-'],
      ['label' => 'Unit',       'value' => $aset['unit'] ?? '-'],
      ['label' => 'Merk',       'value' => $aset['merk'] ?? '-'],
      ['label' => 'Model',      'value' => $aset['model'] ?? '-'],
      ['label' => 'No. Seri',   'value' => $aset['no_seri'] ?? '-',   'mono' => true],
      ['label' => 'Kapasitas',  'value' => $aset['kapasitas'] ?? '-'],
      ['label' => 'Tahun',      'value' => $aset['tahun'] ?? '-'],
      ['label' => 'Kondisi',    'value' => $aset['kondisi'] ?? '-',   'badge' => true],
      ['label' => 'Status',     'value' => $aset['status'] ?? '-',    'badge' => true],
    ];
    foreach ($fields as $f):
      $val = $f['value'];
      $kondisiBadge = match($val) {
        'Baik'          => 'badge bg-emerald-100 text-emerald-700',
        'Kurang Baik'   => 'badge bg-amber-100 text-amber-700',
        'Rusak Ringan'  => 'badge bg-orange-100 text-orange-700',
        'Rusak Berat'   => 'badge bg-red-100 text-red-600',
        'Aktif'         => 'badge bg-emerald-100 text-emerald-700',
        'Tidak Aktif'   => 'badge bg-gray-100 text-gray-500',
        default         => 'badge bg-gray-100 text-gray-500',
      };
    ?>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1"><?= esc($f['label']) ?></p>
      <?php if (!empty($f['badge'])): ?>
        <span class="<?= $kondisiBadge ?>"><?= esc($val) ?></span>
      <?php elseif (!empty($f['mono'])): ?>
        <p class="text-sm font-mono font-semibold text-gray-800"><?= esc($val) ?></p>
      <?php else: ?>
        <p class="text-sm font-medium text-gray-800"><?= esc($val) ?></p>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php if (!empty($aset['keterangan'])): ?>
    <div class="col-span-2 md:col-span-3">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Keterangan</p>
      <p class="text-sm text-gray-700 leading-relaxed"><?= esc($aset['keterangan']) ?></p>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Last Seen -->
<?php if (!empty($aset['last_seen_at'])): ?>
<div class="card p-4 mb-6 flex items-center gap-4 border-l-4 border-emerald-400">
  <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
  </div>
  <div class="flex-1 min-w-0">
    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Terakhir Terlihat</p>
    <p class="text-sm font-semibold text-gray-800 mt-0.5">
      <?= tgl($aset['last_seen_at'], 'd/m/Y H:i') ?>
      &middot; oleh <?= esc($aset['last_seen_by'] ?? 'Anonim') ?>
    </p>
  </div>
  <?php if (!empty($aset['last_seen_lat']) && !empty($aset['last_seen_lng'])): ?>
  <a href="https://maps.google.com/?q=<?= $aset['last_seen_lat'] ?>,<?= $aset['last_seen_lng'] ?>"
     target="_blank"
     class="text-xs text-indigo-500 hover:underline flex-shrink-0">
    Lihat peta
  </a>
  <?php endif; ?>
</div>
<?php else: ?>
<div class="card p-4 mb-6 flex items-center gap-3 border-l-4 border-gray-200" id="geo-status">
  <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
    </svg>
  </div>
  <p class="text-sm text-gray-400">Belum pernah terdeteksi lokasinya. Scan QR untuk mulai tracking.</p>
</div>
<?php endif; ?>

<!-- Komponen Aset -->
<?php if (!empty($komponen)): ?>
<div class="card p-6 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-sm font-semibold text-gray-700">Komponen Aset</h2>
    <span class="text-xs text-gray-400"><?= count($komponen) ?> komponen</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-l-xl">Nama Komponen</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kondisi</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Asal</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-r-xl">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($komponen as $k): ?>
        <?php
          $kondisiBadge = match($k['kondisi'] ?? '') {
            'Baik'        => 'bg-emerald-100 text-emerald-700',
            'Kurang Baik' => 'bg-amber-100 text-amber-700',
            'Rusak'       => 'bg-red-100 text-red-600',
            'Tidak Ada'   => 'bg-gray-100 text-gray-400 line-through',
            default       => 'bg-gray-100 text-gray-500',
          };
          $asalBadge = ($k['asal'] ?? '') === 'Hasil Kanibal'
            ? 'bg-amber-100 text-amber-700'
            : 'bg-blue-100 text-blue-700';
        ?>
        <tr>
          <td class="px-4 py-3 font-medium text-gray-800 komponen-nama"><?= esc($k['nama_komponen'] ?? '-') ?></td>
          <td class="px-4 py-3"><span class="badge <?= $kondisiBadge ?> text-[10px]"><?= esc($k['kondisi'] ?? '-') ?></span></td>
          <td class="px-4 py-3"><span class="badge <?= $asalBadge ?> text-[10px]"><?= esc($k['asal'] ?? 'Original') ?></span></td>
          <td class="px-4 py-3 text-gray-500 text-xs"><?= esc($k['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- Riwayat Kanibal -->
<?php if (!empty($riwayatKanibal)): ?>
<div class="card p-6 mb-6 border-l-4 border-amber-400">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-sm font-semibold text-gray-700">Riwayat Kanibal</h2>
    <a href="/ipsrs/kanibal" class="text-xs font-medium text-indigo-600 hover:underline">Lihat Semua</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-l-xl">Tanggal</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Komponen</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Arah</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Teknisi</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-r-xl">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($riwayatKanibal as $rk): ?>
        <tr>
          <td class="px-4 py-3 text-gray-600"><?= tgl($rk['tanggal'] ?? '') ?></td>
          <td class="px-4 py-3 font-medium text-gray-800"><?= esc($rk['nama_komponen'] ?? '-') ?></td>
          <td class="px-4 py-3">
            <?php if (($rk['id_aset_donor'] ?? '') === $id): ?>
            <span class="badge bg-red-100 text-red-600 text-[10px]">Donor (Dipanen)</span>
            <?php elseif (($rk['id_aset_penerima'] ?? '') === $id): ?>
            <span class="badge bg-emerald-100 text-emerald-700 text-[10px]">Penerima (Diperbaiki)</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-gray-600"><?= esc($rk['petugas'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-500 text-xs"><?= esc($rk['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- Riwayat Laporan Kerusakan -->
<div class="card p-6 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-sm font-semibold text-gray-700">Riwayat Laporan Kerusakan</h2>
    <a href="/ipsrs/lk/baru" class="text-xs font-medium text-indigo-600 hover:underline">+ Buat LK</a>
  </div>
  <?php if (empty($riwayatLK)): ?>
  <p class="text-sm text-gray-400 text-center py-6">Belum ada laporan kerusakan untuk aset ini.</p>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-l-xl">No. Order</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Keluhan</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Resp.</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-r-xl"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($riwayatLK as $lk): ?>
        <?php
          $s = $lk['status'] ?? '';
          $sb = status_lk_badge($s);
          $rt = (int)($lk['response_time'] ?? 0);
        ?>
        <tr class="hover:bg-gray-50/60 transition-colors">
          <td class="px-4 py-3 font-mono text-xs text-indigo-600 font-semibold"><?= esc($lk['no_order'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-600"><?= tgl($lk['tanggal']) ?></td>
          <td class="px-4 py-3 text-gray-800 max-w-[200px] truncate"><?= esc($lk['keluhan'] ?? '-') ?></td>
          <td class="px-4 py-3"><span class="<?= $sb ?>"><?= esc($s) ?></span></td>
          <td class="px-4 py-3 text-xs <?= $rt > 15 ? 'text-red-600 font-semibold' : 'text-gray-600' ?>">
            <?= $rt > 0 ? $rt.' mnt' : '-' ?>
          </td>
          <td class="px-4 py-3">
            <a href="/ipsrs/lk/<?= esc($lk['id'] ?? '') ?>" class="text-xs text-indigo-500 hover:underline">Detail</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<!-- Riwayat Lokasi -->
<div class="card p-6">
  <h2 class="text-sm font-semibold text-gray-700 mb-4">Riwayat Lokasi</h2>
  <?php if (empty($riwayat)): ?>
  <p class="text-sm text-gray-400 text-center py-8">Belum ada riwayat perpindahan lokasi.</p>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 rounded-xl">
        <tr>
          <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-l-xl">Tanggal</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Dari</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Ke</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Alasan</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Petugas</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-r-xl">Catatan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($riwayat as $r): ?>
        <tr class="hover:bg-gray-50/60 transition-colors">
          <td class="px-4 py-3 text-gray-600"><?= tgl($r['tanggal']) ?></td>
          <td class="px-4 py-3 text-gray-700"><?= esc($r['dari'] ?? $r['lokasi_asal'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-700"><?= esc($r['ke'] ?? $r['lokasi_tujuan'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-600"><?= esc($r['alasan'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-600"><?= esc($r['petugas'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-500 max-w-[200px] truncate"><?= esc($r['catatan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<script>
(function() {
  // Only fire geolocation when page opened via QR scan (?via=qr)
  var params = new URLSearchParams(window.location.search);
  if (params.get('via') !== 'qr') return;

  if (!navigator.geolocation) return;
  navigator.geolocation.getCurrentPosition(function(pos) {
    fetch('/ipsrs/aset/<?= esc($id) ?>/ping', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json', 
        'X-Requested-With': 'XMLHttpRequest',
        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
      },
      body: JSON.stringify({ lat: pos.coords.latitude, lng: pos.coords.longitude })
    }).then(function(r) {
      if (r.ok) {
        var el = document.getElementById('geo-status');
        if (el) {
          el.className = el.className.replace('border-gray-200','border-emerald-400');
          el.querySelector('div').className = el.querySelector('div').className.replace('bg-gray-100','bg-emerald-100');
          el.querySelector('svg').classList.replace('text-gray-400','text-emerald-600');
          el.querySelector('p').textContent = '📍 Lokasi berhasil direkam. Refresh untuk melihat detail.';
        }
      }
    }).catch(function(){});
  }, function(){}, { timeout: 8000, maximumAge: 60000 });
})();
</script>
