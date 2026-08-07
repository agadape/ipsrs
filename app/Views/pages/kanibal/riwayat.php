<?php
$asetList = $aset ?? [];
$riwayatList = $riwayat ?? [];
?>

<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs"
     class="w-8 h-8 flex items-center justify-center rounded-md bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Kanibal Alat</h1>
</div>

<!-- Daftar Aset Kanibal Tersedia -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-4">
    <h2 class="text-sm font-semibold text-slate-800">Daftar Aset Kanibal (Tersedia)</h2>
  </div>
  
  <?php if (!empty($aset_kanibal)): ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left text-sm border-collapse">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">No. Aset</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Aset</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Lokasi</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($aset_kanibal as $ak): ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 font-mono text-xs font-semibold text-red-700"><?= esc($ak['nomor_aset'] ?? '-') ?></td>
          <td class="px-4 py-3 font-medium text-slate-900"><?= esc($ak['nama'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($ak['lokasi'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-500 whitespace-pre-line"><?= esc($ak['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="text-center py-6">
    <p class="text-sm text-slate-500">Tidak ada aset dengan status Kanibal.</p>
  </div>
  <?php endif; ?>
</div>

<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-2">
    <h2 class="text-sm font-semibold text-slate-800">Riwayat Penggunaan Kanibal</h2>
  </div>
  
  <p class="text-sm text-slate-500 mb-6">
    Daftar pencatatan komponen yang diambil dari aset lain (donor) untuk perbaikan aset penerima.
  </p>

  <?php if (!empty($riwayatList)): ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left text-sm border-collapse">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">No. LK</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Aset Donor</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Komponen</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Kondisi</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Teknisi</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($riwayatList as $r): ?>
        <?php
          $donorNama = '-';
          foreach ($asetList as $a) {
            if (($a['id'] ?? '') === ($r['id_series_donor'] ?? '')) {
              $donorNama = ($a['nomor_aset'] ?? '') . ' — ' . ($a['nama'] ?? '');
              break;
            }
          }
        ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 text-slate-600"><?= tgl($r['tanggal'] ?? '') ?></td>
          <td class="px-4 py-3">
            <span class="font-mono text-xs font-semibold text-red-700"><?= esc($r['no_order_lk'] ?? '-') ?></span>
          </td>
          <td class="px-4 py-3 font-medium text-slate-800"><?= esc($donorNama) ?></td>
          <td class="px-4 py-3 text-slate-800"><?= esc($r['nama_komponen'] ?? '-') ?></td>
          <td class="px-4 py-3">
            <?php
              $kondisiBadge = match($r['kondisi_komponen'] ?? '') {
                'Baik'       => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                'Kurang Baik'=> 'bg-amber-50 text-amber-700 border border-amber-200',
                'Rusak'      => 'bg-red-50 text-red-600 border border-red-200',
                default      => 'bg-slate-50 text-slate-500 border border-slate-200',
              };
            ?>
            <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium <?= $kondisiBadge ?>"><?= esc($r['kondisi_komponen'] ?? '-') ?></span>
          </td>
          <td class="px-4 py-3 text-slate-600"><?= esc($r['petugas'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?= esc($r['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="text-center py-12 border border-slate-200 border-dashed rounded-lg">
    <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
      <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
      </svg>
    </div>
    <p class="text-sm text-slate-600 font-medium">Belum ada riwayat kanibal</p>
    <p class="text-xs text-slate-400 mt-1">Riwayat akan muncul setelah pencatatan kanibal pertama kali</p>
  </div>
  <?php endif; ?>
</div>

