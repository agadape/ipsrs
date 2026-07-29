<?php
$asetList = $aset ?? [];
$riwayatList = $riwayat ?? [];
?>

<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs"
     class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 hover:border-gray-200 transition-colors text-gray-500 hover:text-gray-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <h1 class="text-xl font-bold text-gray-800">Riwayat Kanibal Alat</h1>
</div>

<!-- Daftar Aset Kanibal Tersedia -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-4">
    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
      <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
    </div>
    <h2 class="text-sm font-semibold text-gray-700">Daftar Aset Kanibal (Tersedia)</h2>
  </div>
  
  <?php if (!empty($aset_kanibal)): ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-l-xl">No. Aset</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Aset</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lokasi</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-r-xl">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($aset_kanibal as $ak): ?>
        <tr>
          <td class="px-4 py-3 font-mono text-xs text-indigo-600"><?= esc($ak['nomor_aset'] ?? '-') ?></td>
          <td class="px-4 py-3 font-medium text-gray-800"><?= esc($ak['nama'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-600"><?= esc($ak['lokasi'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-500 whitespace-pre-line"><?= esc($ak['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="text-center py-6">
    <p class="text-sm text-gray-400">Tidak ada aset dengan status Kanibal.</p>
  </div>
  <?php endif; ?>
</div>

<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-4">
    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
      <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <h2 class="text-sm font-semibold text-gray-700">Riwayat Penggunaan Kanibal</h2>
  </div>
  
  <p class="text-sm text-gray-500 mb-4">
    Daftar pencatatan komponen yang diambil dari aset lain (donor) untuk perbaikan aset penerima.
  </p>

  <?php if (!empty($riwayatList)): ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-l-xl">Tanggal</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">No. LK</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Aset Donor</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Komponen</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kondisi</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Teknisi</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-r-xl">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($riwayatList as $r): ?>
        <?php
          $donorNama = '-';
          foreach ($asetList as $a) {
            if (($a['id'] ?? '') === ($r['id_aset_donor'] ?? '')) {
              $donorNama = ($a['nomor_aset'] ?? '') . ' — ' . ($a['nama'] ?? '');
              break;
            }
          }
        ?>
        <tr>
          <td class="px-4 py-3 text-gray-600"><?= tgl($r['tanggal'] ?? '') ?></td>
          <td class="px-4 py-3">
            <span class="font-mono text-xs font-semibold text-indigo-600"><?= esc($r['no_order_lk'] ?? '-') ?></span>
          </td>
          <td class="px-4 py-3 text-gray-800"><?= esc($donorNama) ?></td>
          <td class="px-4 py-3 font-medium text-gray-800"><?= esc($r['nama_komponen'] ?? '-') ?></td>
          <td class="px-4 py-3">
            <?php
              $kondisiBadge = match($r['kondisi_komponen'] ?? '') {
                'Baik'       => 'bg-emerald-100 text-emerald-700',
                'Kurang Baik'=> 'bg-amber-100 text-amber-700',
                'Rusak'      => 'bg-red-100 text-red-600',
                default      => 'bg-gray-100 text-gray-500',
              };
            ?>
            <span class="badge <?= $kondisiBadge ?> text-[10px]"><?= esc($r['kondisi_komponen'] ?? '-') ?></span>
          </td>
          <td class="px-4 py-3 text-gray-600"><?= esc($r['petugas'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-500 text-xs"><?= esc($r['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="text-center py-12">
    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 flex items-center justify-center">
      <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
      </svg>
    </div>
    <p class="text-sm text-gray-400 font-medium">Belum ada riwayat kanibal</p>
    <p class="text-xs text-gray-300 mt-1">Riwayat akan muncul setelah pencatatan kanibal pertama kali</p>
  </div>
  <?php endif; ?>
</div>
