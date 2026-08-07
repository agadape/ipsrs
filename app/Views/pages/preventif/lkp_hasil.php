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
    : ($hasil === 'Perlu Perbaikan' ? 'badge bg-amber-100 text-amber-700' : 'badge bg-gray-100 text-gray-600');
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div class="flex items-center gap-3">
    <a href="/ipsrs/preventif"
       class="w-8 h-8 flex items-center justify-center rounded-md bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700 shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </a>
    <div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Hasil Lembar Kerja Preventif</h1>
      <p class="text-sm font-medium text-red-700 mt-1"><?= esc($jadwal['aset'] ?? $jadwal['nama_aset'] ?? '-') ?></p>
    </div>
  </div>
  <?php if ($lkp): ?>
  <button onclick="window.print()"
          class="no-print inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium px-4 py-2 rounded-md transition-colors shadow-sm border border-slate-200">
    🖨️ Cetak
  </button>
  <?php endif; ?>
</div>

<?php if (!$lkp): ?>
<div class="card p-6 border-l-4 border-slate-200">
  <p class="text-sm text-slate-500">Belum ada LKP tersimpan untuk jadwal ini.
    <a href="/ipsrs/preventif/lkp/<?= esc($jid) ?>" class="text-red-700 hover:underline font-medium">Isi LKP sekarang →</a>
  </p>
</div>
<?php else: ?>

<!-- Header LKP -->
<div class="card p-6 mb-6">
  <div class="flex flex-wrap items-start justify-between gap-4 mb-5 pb-4 border-b border-slate-100">
    <div class="flex items-center gap-3 flex-wrap">
      <span class="font-mono text-lg font-bold text-red-700 tracking-tight"><?= esc($lkp['no_order'] ?? '-') ?></span>
      <span class="<?= $hasilBadge ?> border border-transparent"><?= esc($hasil ?: '-') ?></span>
      <?php if (!empty($lkp['kategori'])): ?>
      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-800 border border-red-200"><?= esc($lkp['kategori']) ?></span>
      <?php endif; ?>
    </div>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-4">
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Pemeriksaan</p>
      <p class="text-sm font-medium text-slate-800"><?= tgl($lkp['tanggal_pemeriksaan']) ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Lokasi</p>
      <p class="text-sm font-medium text-slate-800"><?= esc($jadwal['lokasi'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Teknisi</p>
      <p class="text-sm font-medium text-slate-800"><?= esc($lkp['teknisi'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pengguna / TTD</p>
      <p class="text-sm font-medium text-slate-800"><?= esc($lkp['nama_user_ttd'] ?? '-') ?></p>
    </div>
  </div>
</div>

<!-- Checklist -->
<div class="card p-6 mb-6">
  <h2 class="text-sm font-bold text-slate-800 mb-4 pb-3 border-b border-slate-200">Checklist Pemeriksaan</h2>
  <?php if (empty($detail)): ?>
  <p class="text-sm text-slate-500">Tidak ada item checklist tersimpan.</p>
  <?php else: ?>
  <div class="space-y-8">
    <?php foreach ($grup as $jenis => $items): ?>
    <?php if (empty($items)) continue; ?>
    <?php
      $badge = $jenis === 'Inspeksi' ? 'bg-red-50 text-red-800 border-red-200'
             : ($jenis === 'Service' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200');
    ?>
    <div>
      <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold <?= $badge ?> border mb-3"><?= esc($jenis) ?></span>
      <div class="overflow-x-auto p-0">
        <table class="w-full text-left text-sm border-collapse">
          <thead class="bg-slate-50 border-b border-slate-200 border-t">
            <tr>
              <th class="py-2.5 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">No</th>
              <th class="py-2.5 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Komponen</th>
              <th class="py-2.5 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Hasil</th>
              <th class="py-2.5 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php foreach ($items as $d): ?>
            <?php
              $h = $d['hasil_inspeksi'] ?? $d['hasil_service'] ?? $d['nilai_pengukuran'] ?? '-';
              if ($jenis === 'Pengukuran' && !empty($d['satuan'])) $h = trim($h . ' ' . $d['satuan']);
              $hClass = in_array($d['hasil_inspeksi'] ?? $d['hasil_service'] ?? '', ['Tidak'])
                  ? 'text-red-600 font-semibold' : 'text-slate-800';
            ?>
            <tr class="hover:bg-slate-50 transition-colors group">
              <td class="px-4 py-3 text-xs text-slate-400 font-mono align-top"><?= esc($d['no_item'] ?? '') ?></td>
              <td class="px-4 py-3 text-sm font-medium text-slate-900 align-top"><?= esc($d['nama_komponen'] ?? '-') ?></td>
              <td class="px-4 py-3 text-sm align-top <?= $hClass ?>">
                <?php if (in_array($h, ['Baik', 'Ya'])): ?>
                  <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[10px] font-bold uppercase tracking-wider">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <?= esc($h) ?>
                  </span>
                <?php elseif (in_array($h, ['Tidak'])): ?>
                  <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-red-50 text-red-600 border border-red-200 rounded text-[10px] font-bold uppercase tracking-wider">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    <?= esc($h) ?>
                  </span>
                <?php else: ?>
                  <span class="font-mono bg-slate-50 border border-slate-200 px-2 py-1 rounded text-slate-700 text-xs font-semibold"><?= esc($h ?: '-') ?></span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 text-xs text-slate-500 align-top italic"><?= esc($d['keterangan'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php if (!empty($lkp['catatan'])): ?>
<div class="card p-6">
  <h2 class="text-sm font-bold text-slate-800 mb-2 pb-2 border-b border-slate-100">Catatan</h2>
  <p class="text-sm text-slate-700 leading-relaxed"><?= esc($lkp['catatan']) ?></p>
</div>
<?php endif; ?>

<?php endif; ?>

<style>
  @media print {
    .no-print, #sidebar, header { display: none !important; }
    main { margin: 0 !important; padding: 0 !important; }
  }
</style>
