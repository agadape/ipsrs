<?php
$id = $series['id'] ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div class="flex items-center gap-3">
    <?php if (session('user_id')): ?>
    <a href="/ipsrs/aset"
       class="w-8 h-8 flex items-center justify-center rounded-md bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700 shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </a>
    <?php endif; ?>
    <div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight"><?= esc($aset['nama'] ?? 'Detail Aset') ?></h1>
      <p class="text-xs font-mono font-medium text-red-700 mt-1"><?= esc($series['nomor_aset'] ?? $id) ?></p>
    </div>
  </div>
  <?php if (session('user_id')): ?>
  <div class="flex items-center gap-2">
    <?php $s = $series['status'] ?? 'Tersedia'; ?>
    
    <?php if ($s === 'Tersedia'): ?>
      <button type="button" onclick="openPeminjamanModal('<?= esc($id) ?>')" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors shadow-sm">Pinjamkan</button>
      <a href="/ipsrs/aset/mutasi?id=<?= esc($id) ?>" class="inline-flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors shadow-sm">Mutasikan</a>
    <?php elseif ($s === 'Dipinjam'): ?>
      <button type="button" onclick="openPengembalianModal('<?= esc($id) ?>')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors shadow-sm">Kembalikan</button>
    <?php elseif ($s === 'Dalam Perbaikan'): ?>
      <a href="/ipsrs/lk" class="inline-flex items-center gap-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors shadow-sm">Lihat LK</a>
    <?php elseif ($s === 'Rusak Berat'): ?>
      <button type="button" onclick="openPenghapusanModal('<?= esc($id) ?>')" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors shadow-sm">Lakukan Penghapusan</button>
      <a href="/ipsrs/kanibal?id=<?= esc($id) ?>" class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors shadow-sm">Ambil Komponen (Kanibal)</a>
    <?php elseif ($s === 'Dihapuskan'): ?>
      <button type="button" onclick="viewBA('<?= esc($id) ?>')" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors shadow-sm">Lihat BA</button>
    <?php endif; ?>

    <?php if ($s !== 'Dihapuskan'): ?>
      <a href="/ipsrs/aset/<?= esc($id) ?>/qr" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold px-3 py-2 rounded-md transition-colors shadow-sm border border-slate-200" title="QR Code">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4z"/></svg>
      </a>
      <a href="/ipsrs/aset/<?= esc($id) ?>/edit" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold px-3 py-2 rounded-md transition-colors shadow-sm border border-slate-200" title="Edit Data">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
      </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Info Grid -->
<div class="card p-6 mb-6">
  <h2 class="text-sm font-semibold text-slate-800 mb-5 pb-3 border-b border-slate-100">Informasi Aset</h2>
  <div class="grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-6">
    <?php
    $fields = [
      ['label' => 'Nomor Aset', 'value' => $series['nomor_aset'] ?? '-', 'mono' => true],
      ['label' => 'Nama',       'value' => $aset['nama'] ?? '-'],
      ['label' => 'Jenis',      'value' => $aset['jenis'] ?? '-'],
      ['label' => 'Kategori',   'value' => $aset['kategori'] ?? '-'],
      ['label' => 'Lokasi',     'value' => $series['lokasi'] ?? '-'],
      ['label' => 'Gedung',     'value' => $series['gedung'] ?? '-'],
      ['label' => 'Lantai',     'value' => $series['lantai'] ?? '-'],
      ['label' => 'Ruangan',    'value' => $series['ruangan'] ?? '-'],
      ['label' => 'Unit',       'value' => $series['unit'] ?? '-'],
      ['label' => 'Merk',       'value' => $aset['merk'] ?? '-'],
      ['label' => 'Model',      'value' => $aset['model'] ?? '-'],
      ['label' => 'No. Seri',   'value' => $series['no_seri'] ?? '-',   'mono' => true],
      ['label' => 'Kapasitas',  'value' => $aset['kapasitas'] ?? '-'],
      ['label' => 'Tahun',      'value' => $series['tahun'] ?? '-'],
      ['label' => 'Kondisi',    'value' => $series['kondisi'] ?? '-',   'badge' => true],
      ['label' => 'Status',     'value' => $series['status'] ?? '-',    'badge' => true],
    ];
    foreach ($fields as $f):
      $val = $f['value'];
      $kondisiBadge = match($val) {
        'Baik'          => 'badge bg-emerald-50 text-emerald-700 border-emerald-200',
        'Kurang Baik'   => 'badge bg-amber-50 text-amber-700 border-amber-200',
        'Rusak Ringan'  => 'badge bg-orange-50 text-orange-700 border-orange-200',
        'Rusak Berat'   => 'badge bg-red-50 text-red-600 border-red-200',
        'Aktif'         => 'badge bg-emerald-50 text-emerald-700 border-emerald-200',
        'Tidak Aktif'   => 'badge bg-slate-50 text-slate-500 border-slate-200',
        default         => 'badge bg-slate-50 text-slate-500 border-slate-200',
      };
    ?>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1"><?= esc($f['label']) ?></p>
      <?php if (!empty($f['badge'])): ?>
        <span class="<?= $kondisiBadge ?>"><?= esc($val) ?></span>
      <?php elseif (!empty($f['mono'])): ?>
        <p class="text-sm font-mono font-semibold text-slate-800"><?= esc($val) ?></p>
      <?php else: ?>
        <p class="text-sm font-medium text-slate-800"><?= esc($val) ?></p>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php if (!empty($aset['keterangan'])): ?>
    <div class="col-span-2 md:col-span-3">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Keterangan</p>
      <p class="text-sm text-slate-700 leading-relaxed"><?= esc($aset['keterangan']) ?></p>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Last Seen -->
<?php if (!empty($series['last_seen_at'])): ?>
<div class="card p-4 mb-6 flex items-center gap-4 border-l-4 border-emerald-500">
  <div class="w-10 h-10 rounded-md bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
  </div>
  <div class="flex-1 min-w-0">
    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Terakhir Terlihat</p>
    <p class="text-sm font-semibold text-slate-900 mt-0.5">
      <?= tgl($series['last_seen_at'], 'd/m/Y H:i') ?>
      <span class="text-slate-400 font-normal ml-1">&middot; oleh <?= esc($aset['last_seen_by'] ?? 'Anonim') ?></span>
    </p>
  </div>
  <?php if (!empty($aset['last_seen_lat']) && !empty($aset['last_seen_lng'])): ?>
  <a href="https://maps.google.com/?q=<?= $aset['last_seen_lat'] ?>,<?= $aset['last_seen_lng'] ?>"
     target="_blank"
     class="text-xs font-medium text-red-700 hover:underline flex-shrink-0">
    Lihat Peta &rarr;
  </a>
  <?php endif; ?>
</div>
<?php else: ?>
<div class="card p-4 mb-6 flex items-center gap-3 border-l-4 border-slate-200" id="geo-status">
  <div class="w-10 h-10 rounded-md bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
    </svg>
  </div>
  <p class="text-sm text-slate-500">Belum pernah terdeteksi lokasinya. Scan QR untuk mulai tracking.</p>
</div>
<?php endif; ?>

<!-- Komponen Aset -->
<?php if (!empty($komponen)): ?>
<div class="card p-6 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-sm font-semibold text-slate-800">Komponen Aset</h2>
    <span class="text-xs font-medium text-slate-500 bg-slate-50 px-2.5 py-1 rounded-full border border-slate-200"><?= count($komponen) ?> komponen</span>
  </div>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Komponen</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Kondisi</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Asal</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($komponen as $k): ?>
        <?php
          $kondisiBadge = match($k['kondisi'] ?? '') {
            'Baik'        => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Kurang Baik' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Rusak'       => 'bg-red-50 text-red-600 border-red-200',
            'Tidak Ada'   => 'bg-slate-50 text-slate-400 border-slate-200 line-through',
            default       => 'bg-slate-50 text-slate-500 border-slate-200',
          };
          $asalBadge = ($k['asal'] ?? '') === 'Hasil Kanibal'
            ? 'bg-amber-50 text-amber-700 border-amber-200'
            : 'bg-red-50 text-red-800 border-red-200';
        ?>
        <tr class="hover:bg-slate-50 transition-colors">
          <td class="px-4 py-3 font-medium text-slate-800"><?= esc($k['nama_komponen'] ?? '-') ?></td>
          <td class="px-4 py-3"><span class="badge <?= $kondisiBadge ?> text-[10px]"><?= esc($k['kondisi'] ?? '-') ?></span></td>
          <td class="px-4 py-3"><span class="badge <?= $asalBadge ?> text-[10px]"><?= esc($k['asal'] ?? 'Original') ?></span></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?= esc($k['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- Riwayat Kanibal -->
<?php if (!empty($riwayatKanibal)): ?>
<div class="card p-6 mb-6 border-l-4 border-amber-500">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-sm font-semibold text-slate-800">Riwayat Kanibal</h2>
    <a href="/ipsrs/kanibal" class="text-xs font-medium text-red-700 hover:underline">Lihat Semua &rarr;</a>
  </div>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Komponen</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Arah</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Teknisi</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($riwayatKanibal as $rk): ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 text-slate-600"><?= tgl($rk['tanggal'] ?? '') ?></td>
          <td class="px-4 py-3 font-medium text-slate-800"><?= esc($rk['nama_komponen'] ?? '-') ?></td>
          <td class="px-4 py-3">
            <?php if (($rk['id_aset_donor'] ?? '') === $id): ?>
            <span class="badge bg-red-50 text-red-600 border border-red-200 text-[10px]">Donor (Dipanen)</span>
            <?php elseif (($rk['id_aset_penerima'] ?? '') === $id): ?>
            <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px]">Penerima (Diperbaiki)</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-slate-600"><?= esc($rk['petugas'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs max-w-[200px] truncate"><?= esc($rk['keterangan'] ?? '-') ?></td>
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
    <h2 class="text-sm font-semibold text-slate-800">Riwayat Laporan Kerusakan</h2>
    <a href="/ipsrs/lk/baru" class="text-xs font-medium text-red-700 hover:underline">+ Buat LK</a>
  </div>
  <?php if (empty($riwayatLK)): ?>
  <p class="text-sm text-slate-500 text-center py-6 border border-dashed border-slate-200 rounded-md">Belum ada laporan kerusakan untuk aset ini.</p>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">No. Order</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Keluhan</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Resp.</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($riwayatLK as $lk): ?>
        <?php
          $s = $lk['status'] ?? '';
          $sb = status_lk_badge($s);
          $rt = (int)($lk['response_time'] ?? 0);
        ?>
        <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="window.location.href='/ipsrs/lk/<?= esc($lk['id'] ?? '') ?>'">
          <td class="px-4 py-3 font-mono text-xs text-red-700 font-semibold group-hover:text-red-800"><?= esc($lk['no_order'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= tgl($lk['tanggal']) ?></td>
          <td class="px-4 py-3 text-slate-800 max-w-[200px] truncate"><?= esc($lk['keluhan'] ?? '-') ?></td>
          <td class="px-4 py-3"><span class="<?= $sb ?>"><?= esc($s) ?></span></td>
          <td class="px-4 py-3 text-xs <?= $rt > 15 ? 'text-red-600 font-semibold' : 'text-slate-600' ?>">
            <?= $rt > 0 ? $rt.' mnt' : '-' ?>
          </td>
          <td class="px-4 py-3 text-right">
            <span class="text-xs font-medium text-slate-400 group-hover:text-red-700 transition-colors">Detail &rarr;</span>
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
  <h2 class="text-sm font-semibold text-slate-800 mb-4">Riwayat Lokasi</h2>
  <?php if (empty($riwayat)): ?>
  <p class="text-sm text-slate-500 text-center py-6 border border-dashed border-slate-200 rounded-md">Belum ada riwayat perpindahan lokasi.</p>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Dari</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Ke</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Alasan</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Petugas</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Catatan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($riwayat as $r): ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 text-slate-600"><?= tgl($r['tanggal']) ?></td>
          <td class="px-4 py-3 text-slate-700"><?= esc($r['dari'] ?? $r['lokasi_asal'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-700 font-medium"><?= esc($r['ke'] ?? $r['lokasi_tujuan'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($r['alasan'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($r['petugas'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-500 max-w-[200px] truncate"><?= esc($r['catatan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>




<!-- Modals for Asset Lifecycle -->
<script>
function openPeminjamanModal(id) {
    Swal.fire({
        title: 'Pinjamkan Aset',
        html: `
            <form id="peminjamanForm" action="/ipsrs/aset/pinjam" method="POST" class="text-left">
                <input type="hidden" name="id_aset_series" value="${id}">
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Nama Peminjam *</label>
                    <input type="text" name="nama_peminjam" required class="w-full border p-2 rounded">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Unit Peminjam *</label>
                    <input type="text" name="unit_peminjam" required class="w-full border p-2 rounded">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Rencana Kembali *</label>
                    <input type="date" name="tgl_kembali_rencana" required class="w-full border p-2 rounded">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Keterangan</label>
                    <textarea name="keterangan" class="w-full border p-2 rounded"></textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Pinjamkan',
        preConfirm: () => {
            document.getElementById('peminjamanForm').submit();
        }
    });
}

function openPengembalianModal(id) {
    Swal.fire({
        title: 'Kembalikan Aset',
        text: 'Aset ini akan dikembalikan dan statusnya menjadi Tersedia.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kembalikan',
        preConfirm: () => {
            window.location.href = '/ipsrs/aset/kembali/' + id;
        }
    });
}

function openPenghapusanModal(id) {
    Swal.fire({
        title: 'Lakukan Penghapusan Aset',
        html: `
            <form id="penghapusanForm" action="/ipsrs/aset/hapus" method="POST" enctype="multipart/form-data" class="text-left">
                <input type="hidden" name="id_aset_series" value="${id}">
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">No. Berita Acara *</label>
                    <input type="text" name="no_ba" required class="w-full border p-2 rounded">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Tanggal BA *</label>
                    <input type="date" name="tgl_ba" required class="w-full border p-2 rounded">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Tindak Lanjut *</label>
                    <select name="tindak_lanjut" required class="w-full border p-2 rounded">
                        <option value="Dijual">Dijual / Lelang</option>
                        <option value="Dihibahkan">Dihibahkan</option>
                        <option value="Dibuang">Dibuang</option>
                        <option value="Dimusnahkan">Dimusnahkan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Upload BA (PDF/Image)</label>
                    <input type="file" name="file_dokumen_ba" class="w-full border p-2 rounded">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Keterangan</label>
                    <textarea name="keterangan" class="w-full border p-2 rounded"></textarea>
                </div>
                <p class="text-xs text-red-600 mt-2">Peringatan: Setelah dihapuskan, aset tidak akan dapat digunakan lagi di sistem ini.</p>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Selesaikan Penghapusan',
        confirmButtonColor: '#dc2626',
        preConfirm: () => {
            document.getElementById('penghapusanForm').submit();
        }
    });
}

function viewBA(id) {
    window.location.href = '/ipsrs/aset/ba/' + id;
}
</script>