<?php
$jid    = $jadwal['id'] ?? '';
$lkp    = $lkp ?? null;
$detail = $detail ?? [];

// Kelompokkan detail per jenis
$grup = [];
foreach ($detail as $d) {
    $j = $d['jenis_item'] ?: 'Pemeriksaan';
    $grup[$j][] = $d;
}
$hasil = $lkp['hasil_pemeriksaan'] ?? '';
$hasilBadge = $hasil === 'Siap Pakai'
    ? 'badge bg-emerald-100 text-emerald-700'
    : ($hasil === 'Perlu Perbaikan' ? 'badge bg-amber-100 text-amber-700' : 'badge bg-[#202532] text-gray-300');
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div class="flex items-center gap-3">
    <a href="/ipsrs/preventif"
       class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#121620]/60 shadow-[0_4px_20px_rgba(0,0,0,0.5)] border border-white/5 hover:border-gray-200 transition-colors text-gray-400 hover:text-gray-700">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </a>
    <div>
      <h1 class="text-xl font-bold text-gray-100">Hasil Lembar Kerja Preventif</h1>
      <p class="text-sm font-medium text-[#CCFF00] mt-0.5"><?= esc($jadwal['aset'] ?? $jadwal['nama_aset'] ?? '-') ?></p>
    </div>
  </div>
  <?php if ($lkp): ?>
  <button onclick="window.print()"
          class="no-print inline-flex items-center gap-2 bg-[#121620]/60 hover:bg-white/5 text-gray-200 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors shadow-[0_4px_20px_rgba(0,0,0,0.5)] border border-white/10">
    🖨️ Cetak
  </button>
  <?php endif; ?>
</div>

<?php if (!$lkp): ?>
<div class="card p-6 border-l-4 border-white/10">
  <p class="text-sm text-gray-400">Belum ada LKP tersimpan untuk jadwal ini.
    <a href="/ipsrs/preventif/lkp/<?= esc($jid) ?>" class="text-[#CCFF00] hover:underline">Isi LKP sekarang →</a>
  </p>
</div>
<?php else: ?>

<!-- Header LKP -->
<div class="card p-6 mb-6">
  <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
    <div class="flex items-center gap-3 flex-wrap">
      <span class="font-mono text-lg font-bold text-[#CCFF00]"><?= esc($lkp['no_order'] ?? '-') ?></span>
      <span class="<?= $hasilBadge ?>"><?= esc($hasil ?: '-') ?></span>
      <?php if (!empty($lkp['kategori'])): ?>
      <span class="badge bg-[#CCFF00]/10 text-[#CCFF00]"><?= esc($lkp['kategori']) ?></span>
      <?php endif; ?>
    </div>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-4">
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tanggal Pemeriksaan</p>
      <p class="text-sm font-medium text-gray-100"><?= tgl($lkp['tanggal_pemeriksaan']) ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Lokasi</p>
      <p class="text-sm font-medium text-gray-100"><?= esc($jadwal['lokasi'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Teknisi</p>
      <p class="text-sm font-medium text-gray-100"><?= esc($lkp['teknisi'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Pengguna / TTD</p>
      <p class="text-sm font-medium text-gray-100"><?= esc($lkp['nama_user_ttd'] ?? '-') ?></p>
    </div>
  </div>
</div>

<!-- Checklist -->
<div class="card p-6 mb-6">
  <h2 class="text-sm font-semibold text-gray-200 mb-4 pb-3 border-b border-white/5">Checklist Pemeriksaan</h2>
  <?php if (empty($detail)): ?>
  <p class="text-sm text-gray-400">Tidak ada item checklist tersimpan.</p>
  <?php else: ?>
  <div class="space-y-6">
    <?php foreach ($grup as $jenis => $items): ?>
    <?php if (empty($items)) continue; ?>
    <?php
      $badge = $jenis === 'Inspeksi' ? 'bg-[#CCFF00]/10 text-indigo-700'
             : ($jenis === 'Service' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700');
    ?>
    <div>
      <span class="badge <?= $badge ?> mb-2"><?= esc($jenis) ?></span>
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-[10px] uppercase tracking-wider text-gray-400">
            <th class="pb-2 pr-3 font-semibold">No</th>
            <th class="pb-2 pr-3 font-semibold">Komponen</th>
            <th class="pb-2 pr-3 font-semibold">Hasil</th>
            <th class="pb-2 font-semibold">Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $d): ?>
          <?php
            $h = $d['hasil_inspeksi'] ?? $d['hasil_service'] ?? $d['nilai_pengukuran'] ?? '-';
            if ($jenis === 'Pengukuran' && !empty($d['satuan'])) $h = trim($h . ' ' . $d['satuan']);
            $hClass = in_array($d['hasil_inspeksi'] ?? $d['hasil_service'] ?? '', ['Tidak'])
                ? 'text-red-600 font-semibold' : 'text-gray-100';
          ?>
          <tr class="border-b border-gray-50">
            <td class="py-2.5 pr-3 text-xs text-gray-400 align-top"><?= esc($d['no_item'] ?? '') ?></td>
            <td class="py-2.5 pr-3 text-sm font-medium text-gray-100 align-top"><?= esc($d['nama_komponen'] ?? '-') ?></td>
            <td class="py-2.5 pr-3 text-sm align-top <?= $hClass ?>">
              <?php if (in_array($h, ['Baik', 'Ya'])): ?>
                <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-emerald-50 text-emerald-700 rounded-md text-[11px] font-bold uppercase tracking-wider">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  <?= esc($h) ?>
                </span>
              <?php elseif (in_array($h, ['Tidak'])): ?>
                <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-red-50 text-red-600 rounded-md text-[11px] font-bold uppercase tracking-wider">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                  <?= esc($h) ?>
                </span>
              <?php else: ?>
                <span class="font-mono bg-[#181C25]/80 px-2 py-1 rounded text-gray-200 text-xs"><?= esc($h ?: '-') ?></span>
              <?php endif; ?>
            </td>
            <td class="py-2.5 text-xs text-gray-400 align-top italic"><?= esc($d['keterangan'] ?? '') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php if (!empty($lkp['catatan'])): ?>
<div class="card p-6">
  <h2 class="text-sm font-semibold text-gray-200 mb-2">Catatan</h2>
  <p class="text-sm text-gray-200 leading-relaxed"><?= esc($lkp['catatan']) ?></p>
</div>
<?php endif; ?>

<?php endif; ?>

<style>
  @media print {
    .no-print, #sidebar, header { display: none !important; }
    main { margin: 0 !important; padding: 0 !important; }
  }
</style>
