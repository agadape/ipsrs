<?php
$asetList = $aset ?? [];
$riwayatList = $riwayat ?? [];
?>

<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs"
     class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#121620]/60 shadow-[0_4px_20px_rgba(0,0,0,0.5)] border border-white/5 hover:border-gray-200 transition-colors text-gray-400 hover:text-gray-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <h1 class="text-xl font-bold text-gray-100">Riwayat Kanibal Alat</h1>
</div>

<div class="card p-6 mb-6">
  <p class="text-sm text-gray-400 mb-4">
    Daftar pencatatan komponen yang diambil dari aset lain (donor) untuk perbaikan aset penerima.
  </p>

  <?php if (!empty($riwayatList)): ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-[#181C25]/80">
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
          <td class="px-4 py-3 text-gray-300"><?= tgl($r['tanggal'] ?? '') ?></td>
          <td class="px-4 py-3">
            <span class="font-mono text-xs font-semibold text-[#CCFF00]"><?= esc($r['no_order_lk'] ?? '-') ?></span>
          </td>
          <td class="px-4 py-3 text-gray-100"><?= esc($donorNama) ?></td>
          <td class="px-4 py-3 font-medium text-gray-100"><?= esc($r['nama_komponen'] ?? '-') ?></td>
          <td class="px-4 py-3">
            <?php
              $kondisiBadge = match($r['kondisi_komponen'] ?? '') {
                'Baik'       => 'bg-emerald-100 text-emerald-700',
                'Kurang Baik'=> 'bg-amber-100 text-amber-700',
                'Rusak'      => 'bg-red-100 text-red-600',
                default      => 'bg-[#202532] text-gray-400',
              };
            ?>
            <span class="badge <?= $kondisiBadge ?> text-[10px]"><?= esc($r['kondisi_komponen'] ?? '-') ?></span>
          </td>
          <td class="px-4 py-3 text-gray-300"><?= esc($r['petugas'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-400 text-xs"><?= esc($r['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="text-center py-12">
    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-[#202532] flex items-center justify-center">
      <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
      </svg>
    </div>
    <p class="text-sm text-gray-400 font-medium">Belum ada riwayat kanibal</p>
    <p class="text-xs text-gray-300 mt-1">Riwayat akan muncul setelah pencatatan kanibal pertama kali</p>
  </div>
  <?php endif; ?>
</div>
