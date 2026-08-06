<?php
$authRole = session('user_role') ?? '';
$id     = $lk['id'] ?? '';
$status = $lk['status'] ?? '';

$statusSteps = [
    ['label' => 'Laporan Masuk', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
    ['label' => 'Didisposisi', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
    ['label' => 'Survei', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>'],
    ['label' => 'Dalam Perbaikan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
    ['label' => 'Selesai', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>'],
];
$currentStep = 0;
foreach ($statusSteps as $i => $step) {
    if ($step['label'] === $status) $currentStep = $i;
}
if (in_array($status, ['Menunggu Suku Cadang', 'Menunggu Vendor'])) $currentStep = 3;

$sBadge = status_lk_badge($status);

$canAssign   = $status === 'Laporan Masuk';
$canProgress = in_array($status, ['Didisposisi', 'Survei', 'Dalam Perbaikan', 'Menunggu Suku Cadang', 'Menunggu Vendor']);
$isSelesai   = $status === 'Selesai';

$sukuCadang = $sukuCadang ?? [];
$vendorDetail = $vendorDetail ?? [];

$showSC      = !in_array($status, ['Laporan Masuk', 'Didisposisi']) && ($authRole !== 'pelapor' || !empty($sukuCadang));
$showVendor  = (($lk['proses'] ?? '') === 'III' || in_array($status, ['Menunggu Vendor']) || !empty($vendorDetail)) && ($authRole !== 'pelapor' || !empty($vendorDetail));

$prosesLabels = ['I' => 'Proses I — Perbaikan Langsung', 'II' => 'Proses II — Pakai Suku Cadang', 'III' => 'Proses III — Vendor Eksternal'];
?>

<!-- Back -->
<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs/lk"
     class="w-8 h-8 flex items-center justify-center rounded-md bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Laporan Kerusakan</h1>
</div>

<!-- LK Header Card -->
<div class="card p-6 mb-6">
  <div class="flex flex-wrap items-start justify-between gap-4 mb-5 pb-4 border-b border-slate-100">
    <div>
      <div class="flex items-center gap-3 flex-wrap mb-2">
        <span class="font-mono text-lg font-bold text-indigo-600 tracking-tight"><?= esc($lk['no_order'] ?? '-') ?></span>
        <span class="<?= $sBadge ?> border border-transparent"><?= esc($status) ?></span>
        <span class="<?= kode_badge($lk['kode'] ?? '') ?>"><?= esc($lk['kode'] ?? '-') ?></span>
        <?php if (!empty($lk['proses'])): ?>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">Proses <?= esc($lk['proses']) ?></span>
        <?php endif; ?>
      </div>
      <p class="text-slate-500 text-sm font-medium"><?= tgl($lk['tanggal'], 'd F Y') ?>
        <?= !empty($lk['jam_laporan']) ? ' · ' . esc($lk['jam_laporan']) : '' ?>
      </p>
    </div>
    <div class="flex items-center gap-2">
      <?php if (session('user_role') !== 'pelapor' && $status === 'Laporan Masuk'): ?>
      <button onclick="document.getElementById('modal-edit-detail').classList.remove('hidden')" 
         class="flex items-center gap-2 text-xs font-semibold px-3 py-2 rounded-md bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Lengkapi Data
      </button>
      <?php endif; ?>
      <?php if (!$isSelesai): ?>
      <a href="/ipsrs/lk/baru"
         class="flex items-center gap-2 text-xs font-semibold px-3 py-2 rounded-md bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
        + LK Baru
      </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-4">
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pelapor</p>
      <p class="text-sm font-semibold text-slate-900"><?= esc($lk['pelapor'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Unit Pelapor</p>
      <p class="text-sm font-medium text-slate-800"><?= esc($lk['unit_pelapor'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Lokasi</p>
      <p class="text-sm font-medium text-slate-800"><?= esc($lk['lokasi'] ?? '-') ?></p>
    </div>
    <div class="col-span-2 md:col-span-3">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Keluhan</p>
      <p class="text-sm text-slate-800 leading-relaxed font-medium bg-slate-50 p-3 rounded-md border border-slate-100"><?= esc($lk['keluhan'] ?? '-') ?></p>
    </div>
    <?php if (!empty($lk['id_aset_series']) || !empty($lk['nama_aset'])): ?>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Aset Terkait</p>
      <?php if (!empty($lk['id_aset_series'])): ?>
      <a href="/ipsrs/aset/series/<?= esc($lk['id_aset_series']) ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 hover:underline">
        <?= esc($lk['nama_aset'] ?? $lk['id_aset_series']) ?>
      </a>
      <?php else: ?>
      <p class="text-sm font-semibold text-slate-800"><?= esc($lk['nama_aset']) ?></p>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Status Timeline -->
<div class="card p-6 mb-6">
  <h2 class="text-sm font-bold text-slate-800 mb-5 pb-3 border-b border-slate-100">Alur Status</h2>
  <div class="flex items-center justify-between overflow-x-auto pb-6 pt-4 px-4 w-full scrollbar-hide">
    <?php foreach ($statusSteps as $i => $step): ?>
    <?php 
      $done = $i < $currentStep; 
      $current = $i === $currentStep; 
      $isSubStatus = $current && in_array($status, ['Menunggu Suku Cadang', 'Menunggu Vendor']);
    ?>
    <div class="flex flex-col items-center relative z-10 group cursor-default min-w-[80px]">
      <div class="w-12 h-12 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300 transform group-hover:scale-110 shadow-sm
        <?= $done ? 'bg-indigo-600 border-indigo-600 text-white' : ($current ? 'bg-white border-indigo-600 text-indigo-600 ring-4 ring-indigo-50' : 'bg-slate-50 border-slate-200 text-slate-400') ?>">
        <svg class="w-6 h-6 <?= $current && !$isSubStatus ? 'animate-pulse' : '' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <?= $done ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>' : $step['icon'] ?>
        </svg>
      </div>
      <div class="mt-3 text-center">
        <span class="block text-xs font-bold leading-tight transition-colors
          <?= $current ? 'text-indigo-700' : ($done ? 'text-slate-700' : 'text-slate-400') ?>">
          <?= esc($step['label']) ?>
        </span>
        <?php if ($isSubStatus && $step['label'] === 'Dalam Perbaikan'): ?>
        <span class="block text-[10px] font-medium text-amber-600 mt-1 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 whitespace-nowrap">
          <?= esc($status) ?>
        </span>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($i < count($statusSteps) - 1): ?>
    <div class="flex-1 h-1 -mt-8 mx-2 sm:mx-4 rounded-full <?= $done ? 'bg-indigo-600' : 'bg-slate-200' ?> transition-colors duration-500"></div>
    <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>

<!-- ── Completion Details (only when Selesai) ────────────────────────── -->
<?php if ($isSelesai): ?>
<div class="card p-6 mb-6 border border-emerald-200 bg-emerald-50/30">
  <div class="flex items-center gap-2 mb-5">
    <div class="w-7 h-7 rounded-md bg-emerald-100 flex items-center justify-center border border-emerald-200">
      <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <h2 class="text-sm font-bold text-slate-800">Hasil Penanganan</h2>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-4 mb-4">
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Teknisi</p>
      <p class="text-sm font-semibold text-slate-900"><?= esc($lk['teknisi'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jenis Proses</p>
      <p class="text-sm font-semibold text-slate-900">
        <?= !empty($lk['proses']) ? 'Proses ' . esc($lk['proses']) : '-' ?>
      </p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Selesai</p>
      <p class="text-sm font-medium text-slate-800">
        <?= tgl($lk['tanggal_selesai']) ?>
        <?= !empty($lk['jam_selesai']) ? ' ' . esc($lk['jam_selesai']) : '' ?>
      </p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Down Time</p>
      <p class="text-sm font-medium text-slate-800">
        <?= isset($lk['down_time']) && $lk['down_time'] !== null ? (int)$lk['down_time'] . ' menit' : '-' ?>
      </p>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Response Time</p>
      <?php if (isset($lk['response_time']) && $lk['response_time'] !== null): ?>
      <?php $rt = (int)$lk['response_time']; ?>
      <div class="flex items-center gap-2">
        <span class="text-sm font-bold <?= $rt <= 15 ? 'text-emerald-700' : 'text-red-700' ?>"><?= $rt ?> menit</span>
        <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium <?= $rt <= 15 ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200' ?>">
          <?= $rt <= 15 ? 'SLA Terpenuhi ≤15 mnt' : 'Melebihi SLA' ?>
        </span>
      </div>
      <?php else: ?><p class="text-sm text-slate-500">-</p><?php endif; ?>
    </div>
    <?php if (!empty($lk['tindakan'])): ?>
    <div class="md:col-span-1">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tindakan</p>
      <p class="text-sm text-slate-800 leading-relaxed font-medium"><?= esc($lk['tindakan']) ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($lk['ttd_pelapor'])): ?>
    <div class="md:col-span-1 border-t md:border-t-0 md:border-l border-slate-200 pt-4 md:pt-0 md:pl-6">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanda Tangan Pelapor</p>
      <div class="mt-2 w-32 h-20 border border-slate-200 rounded-md overflow-hidden bg-white flex items-center justify-center shadow-sm">
        <img src="<?= esc($lk['ttd_pelapor']) ?>" alt="Tanda Tangan" class="max-w-full max-h-full object-contain">
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- ── Suku Cadang ───────────────────────────────────────────────────── -->
<?php if ($showSC): ?>
<div class="card p-6 mb-6">
  <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
    <h2 class="text-sm font-bold text-slate-800">Suku Cadang Digunakan</h2>
    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($sukuCadang ?? []) ?> item</span>
  </div>

  <?php if (!empty($sukuCadang)): ?>
  <div class="overflow-x-auto mb-4 p-0">
    <table class="w-full text-left text-sm border-collapse">
      <thead class="bg-slate-50 border-b border-slate-200 border-t">
        <tr>
          <th class="py-3 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Nama Barang</th>
          <th class="py-3 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Jumlah</th>
          <th class="py-3 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($sukuCadang as $sc): ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 font-medium text-slate-900"><?= esc($sc['nama_barang'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600 font-semibold"><?= esc($sc['jumlah'] ?? '-') ?> <?= esc($sc['satuan'] ?? '') ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?= esc($sc['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <p class="text-sm text-slate-500 mb-4 bg-slate-50 p-3 rounded-md border border-slate-100">Belum ada suku cadang yang dicatat.</p>
  <?php endif; ?>

  <?php if (!$isSelesai && $authRole !== 'pelapor'): ?>
  <div class="pt-5 border-t border-slate-200 mt-2">
    <p class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4">Tambah Suku Cadang</p>

    <!-- Toggle: Gudang / Kanibal -->
    <div class="flex gap-2 mb-4 p-1 bg-slate-50 border border-slate-200 rounded-md w-fit" id="sc-source-toggle">
      <button type="button" onclick="setScSource('Gudang')"
              id="btn-gudang"
              class="px-4 py-1.5 text-xs font-semibold rounded-sm transition-colors bg-white border border-slate-200 shadow-sm text-slate-900">
        Dari Gudang
      </button>
      <button type="button" onclick="setScSource('Kanibal')"
              id="btn-kanibal"
              class="px-4 py-1.5 text-xs font-semibold rounded-sm transition-colors bg-transparent border border-transparent text-slate-500 hover:text-slate-700">
        Kanibal dari Aset Lain
      </button>
    </div>
    <input type="hidden" name="sumber" id="sc-source" value="Gudang">

    <!-- Form Gudang (default) -->
    <form method="POST" action="/ipsrs/lk/<?= esc($id) ?>/suku-cadang" id="form-gudang">
      <?= csrf_field() ?>
      <input type="hidden" name="sumber" value="Gudang">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Barang <span class="text-red-500">*</span></label>
          <select name="id_barang" required
                  class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm appearance-none">
            <option value="">-- Pilih Barang --</option>
            <?php foreach (($stokTersedia ?? []) as $s): ?>
            <?php if ((int)($s['stok_tersedia'] ?? 0) > 0): ?>
            <option value="<?= esc($s['id'] ?? '') ?>">
              <?= esc($s['nama'] ?? '') ?> (<?= (int)($s['stok_tersedia'] ?? 0) ?> <?= esc($s['satuan'] ?? '') ?> tersisa)
            </option>
            <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Jumlah <span class="text-red-500">*</span></label>
          <input type="number" name="jumlah" min="1" required
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
        </div>
        <div>
          <button type="submit"
                  class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
            Pakai Barang
          </button>
        </div>
      </div>
    </form>

    <!-- Form Kanibal -->
    <form method="POST" action="/ipsrs/kanibal" id="form-kanibal" class="hidden">
      <?= csrf_field() ?>
      <input type="hidden" name="id_lk" value="<?= esc($id) ?>">
      <input type="hidden" name="no_order_lk" value="<?= esc($lk['no_order'] ?? '') ?>">
      <input type="hidden" name="id_aset_penerima" value="<?= esc($lk['id_aset_series'] ?? '') ?>">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Aset Donor <span class="text-red-500">*</span></label>
          <select name="id_aset_donor" required id="kanibal-donor"
                  class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm appearance-none"
                  onchange="loadKomponenDonor(this.value)">
            <option value="">-- Pilih Aset Donor --</option>
            <?php foreach (($aset ?? []) as $a): ?>
            <?php if (($a['status'] ?? '') === 'Kanibal'): ?>
              <option value="<?= esc($a['id'] ?? '') ?>" data-nama="<?= esc($a['nama'] ?? '') ?>">
                <?= esc(($a['nomor_aset'] ?? '') . ' - ' . ($a['nama'] ?? '')) ?> (<?= esc(($a['ruangan'] ?? '') . ' / ' . ($a['gedung'] ?? '')) ?>)
              </option>
            <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Komponen <span class="text-red-500">*</span></label>
          <input type="text" name="nama_komponen" required placeholder="Contoh: Kompresor, Motor Fan, PCB"
                 list="komponen-donor-list"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
          <datalist id="komponen-donor-list"></datalist>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kondisi Komponen</label>
          <select name="kondisi_komponen"
                  class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm appearance-none">
            <option value="Baik">Baik</option>
            <option value="Kurang Baik">Kurang Baik</option>
            <option value="Rusak">Rusak</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Disetujui Oleh</label>
          <input type="text" name="disetujui_oleh" placeholder="Admin / Ka IPSRS"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
        </div>
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Keterangan</label>
          <input type="text" name="keterangan" placeholder="Catatan kanibal"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
        </div>
      </div>
      <div class="mt-4">
        <button type="submit"
                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
          Catat Kanibal
        </button>
      </div>
    </form>
  </div>

  <script>
  function setScSource(sumber) {
    document.getElementById('sc-source').value = sumber;
    document.getElementById('form-gudang').classList.toggle('hidden', sumber !== 'Gudang');
    document.getElementById('form-kanibal').classList.toggle('hidden', sumber !== 'Kanibal');
    document.getElementById('btn-gudang').className = sumber === 'Gudang'
      ? 'px-4 py-1.5 text-xs font-semibold rounded-sm transition-colors bg-white border border-slate-200 shadow-sm text-slate-900'
      : 'px-4 py-1.5 text-xs font-semibold rounded-sm transition-colors bg-transparent border border-transparent text-slate-500 hover:text-slate-700';
    document.getElementById('btn-kanibal').className = sumber === 'Kanibal'
      ? 'px-4 py-1.5 text-xs font-semibold rounded-sm transition-colors bg-white border border-slate-200 shadow-sm text-slate-900'
      : 'px-4 py-1.5 text-xs font-semibold rounded-sm transition-colors bg-transparent border border-transparent text-slate-500 hover:text-slate-700';
  }

  function loadKomponenDonor(idAset) {
    const dl = document.getElementById('komponen-donor-list');
    dl.innerHTML = '';
    if (!idAset) return;
    fetch('/ipsrs/aset/' + idAset)
      .then(r => r.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        doc.querySelectorAll('.komponen-nama').forEach(el => {
          const opt = document.createElement('option');
          opt.value = el.textContent.trim();
          dl.appendChild(opt);
        });
      })
      .catch(() => {});
  }
  </script>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- ── Vendor / Proses III ──────────────────────────────────────────── -->
<?php
$vendorList   = $vendorList ?? [];
?>
<?php if ($showVendor): ?>
<div class="card p-6 mb-6">
  <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
    <h2 class="text-sm font-bold text-slate-800">Vendor / Pihak Ke-3 (Proses III)</h2>
    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($vendorDetail) ?> entri</span>
  </div>

  <?php if (!empty($vendorDetail)): ?>
  <div class="overflow-x-auto mb-4 p-0">
    <table class="w-full text-left text-sm border-collapse">
      <thead class="bg-slate-50 border-b border-slate-200 border-t">
        <tr>
          <th class="py-3 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Vendor</th>
          <th class="py-3 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Tgl Kirim</th>
          <th class="py-3 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Estimasi</th>
          <th class="py-3 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Tgl Kembali</th>
          <th class="py-3 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($vendorDetail as $v): ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 font-medium text-slate-900"><?= esc($v['nama_vendor'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= tgl($v['tanggal_kirim']) ?></td>
          <td class="px-4 py-3 text-slate-600"><?= tgl($v['estimasi_selesai']) ?></td>
          <td class="px-4 py-3 text-slate-600"><?= tgl($v['tanggal_kembali']) ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?= esc($v['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <p class="text-sm text-slate-500 mb-4 bg-slate-50 p-3 rounded-md border border-slate-100">Belum ada data vendor yang dicatat.</p>
  <?php endif; ?>

  <?php if (!$isSelesai && $authRole !== 'pelapor'): ?>
  <div class="pt-5 border-t border-slate-200 mt-2">
    <p class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4">Catat Vendor</p>
    <form method="POST" action="/ipsrs/lk/<?= esc($id) ?>/vendor">
      <?= csrf_field() ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Pilih Vendor <span class="text-red-500">*</span></label>
          <select name="id_vendor" required
                  class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm appearance-none">
            <option value="">-- Pilih Vendor Terdaftar --</option>
            <?php foreach ($vendorList as $vd): ?>
            <option value="<?= esc($vd['id'] ?? '') ?>"><?= esc($vd['nama_vendor'] ?? '') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal Kirim</label>
          <input type="date" name="tanggal_kirim"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Estimasi Selesai</label>
          <input type="date" name="estimasi_selesai"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal Kembali</label>
          <input type="date" name="tanggal_kembali"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
        </div>
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Keterangan / Ringkasan RAB</label>
          <input type="text" name="keterangan" placeholder="Catatan, biaya, atau ringkasan RAB"
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
        </div>
      </div>
      <div class="mt-4">
        <button type="submit"
                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
          Simpan Vendor
        </button>
      </div>
    </form>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- ── Update Status Form (hidden when Selesai or if user is pelapor) ──────────────────────── -->
  <?php 
  $isDataLengkap = !empty($lk['id_aset_series']) && !empty($lk['kode']);
  if (!$isSelesai && $authRole !== 'pelapor'): 
  ?>
  <div class="card p-6">
    <h2 class="text-sm font-bold text-slate-800 mb-5 pb-3 border-b border-slate-100">Update Status</h2>
    
    <?php if ($status === 'Laporan Masuk' && !$isDataLengkap): ?>
    <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-md flex items-start gap-3">
      <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <div>
        <p class="text-sm font-bold tracking-tight">Data Belum Lengkap!</p>
        <p class="text-xs mt-1">Anda tidak dapat mendisposisikan atau memproses laporan ini sebelum menentukan <strong class="font-bold">Kode Pekerjaan</strong> dan menghubungkannya dengan <strong class="font-bold">Aset</strong> di database. Silakan klik tombol <span class="font-bold bg-white px-1 py-0.5 rounded shadow-sm border border-amber-200">Lengkapi Data</span> di bagian atas.</p>
      </div>
    </div>
    <?php else: ?>

    <?php if ($canAssign): ?>
  <!-- Assign teknisi / disposisi -->
  <form method="POST" action="/ipsrs/lk/<?= esc($id) ?>/status">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Teknisi <span class="text-red-500">*</span></label>
        <select name="teknisi" required
                class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm appearance-none">
          <option value="">-- Pilih Teknisi --</option>
          <?php foreach (($teknisiList ?? []) as $t): ?>
          <option value="<?= esc($t['nama_lengkap']) ?>" <?= ($lk['teknisi'] ?? '') === $t['nama_lengkap'] ? 'selected' : '' ?>><?= esc($t['nama_lengkap']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <input type="hidden" name="status_baru" value="Didisposisi">
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal Cek</label>
        <input type="date" name="tanggal_cek" value="<?= esc($lk['tanggal_cek'] ?? date('Y-m-d')) ?>"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Jam Cek</label>
        <input type="time" name="jam_cek" value="<?= esc(substr($lk['jam_cek'] ?? date('H:i'), 0, 5)) ?>"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
      </div>
    </div>
    <div class="mt-5">
      <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
        Tetapkan Teknisi
      </button>
    </div>
  </form>

  <?php elseif ($canProgress): ?>
  <!-- Progress / Selesai -->
  <form method="POST" action="/ipsrs/lk/<?= esc($id) ?>/status">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Teknisi -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Teknisi</label>
        <select name="teknisi"
                class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm appearance-none">
          <option value="">-- Pilih --</option>
          <?php foreach (($teknisiList ?? []) as $t): ?>
          <option value="<?= esc($t['nama_lengkap']) ?>" <?= ($lk['teknisi'] ?? '') === $t['nama_lengkap'] ? 'selected' : '' ?>><?= esc($t['nama_lengkap']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Status Baru -->
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Status Baru <span class="text-red-500">*</span></label>
        
        <input type="hidden" name="status_baru" id="status_baru" required value="">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3" id="status-cards">
          
          <button type="button" onclick="selectStatus('Survei')" class="status-btn flex flex-col items-center justify-center gap-2 p-3 rounded-lg border-2 border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50 transition-all text-slate-600" data-value="Survei">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span class="text-xs font-bold text-center leading-tight">Survei<br><span class="font-medium text-[10px] opacity-80">(Pengecekan)</span></span>
          </button>

          <button type="button" onclick="selectStatus('Dalam Perbaikan')" class="status-btn flex flex-col items-center justify-center gap-2 p-3 rounded-lg border-2 border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50 transition-all text-slate-600" data-value="Dalam Perbaikan">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-xs font-bold text-center leading-tight">Dalam<br>Perbaikan</span>
          </button>

          <button type="button" onclick="selectStatus('Menunggu Suku Cadang')" class="status-btn flex flex-col items-center justify-center gap-2 p-3 rounded-lg border-2 border-slate-200 bg-white hover:border-amber-300 hover:bg-amber-50 transition-all text-slate-600" data-value="Menunggu Suku Cadang">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="text-xs font-bold text-center leading-tight">Menunggu<br>Suku Cadang</span>
          </button>

          <button type="button" onclick="selectStatus('Menunggu Vendor')" class="status-btn flex flex-col items-center justify-center gap-2 p-3 rounded-lg border-2 border-slate-200 bg-white hover:border-purple-300 hover:bg-purple-50 transition-all text-slate-600" data-value="Menunggu Vendor">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span class="text-xs font-bold text-center leading-tight">Menunggu<br>Vendor</span>
          </button>

          <button type="button" onclick="selectStatus('Selesai')" class="status-btn flex flex-col items-center justify-center gap-2 p-3 rounded-lg border-2 border-slate-200 bg-white hover:border-emerald-300 hover:bg-emerald-50 transition-all text-slate-600 col-span-2 md:col-span-1" data-value="Selesai">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span class="text-xs font-bold text-center leading-tight">Selesai</span>
          </button>

        </div>
        <script>
          function selectStatus(val) {
            document.getElementById('status_baru').value = val;
            document.querySelectorAll('.status-btn').forEach(btn => {
              if(btn.dataset.value === val) {
                btn.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-700');
                btn.classList.remove('border-slate-200', 'bg-white', 'text-slate-600');
              } else {
                btn.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-700');
                btn.classList.add('border-slate-200', 'bg-white', 'text-slate-600');
              }
            });
            toggleSignature();
          }
        </script>
      </div>



      <!-- Tindakan -->
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tindakan yang Dilakukan</label>
        <textarea name="tindakan" rows="3"
                  placeholder="Deskripsikan tindakan perbaikan..." required
                  class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm resize-none"><?= esc($lk['tindakan'] ?? '') ?></textarea>
      </div>

      <!-- Tanggal & Jam Selesai -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai" value="<?= esc($lk['tanggal_selesai'] ?? date('Y-m-d')) ?>"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Jam Selesai</label>
        <input type="time" name="jam_selesai" value="<?= esc(substr($lk['jam_selesai'] ?? date('H:i'), 0, 5)) ?>"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
      </div>

      <!-- Tanda Tangan Pelapor (Hidden by default, shown when Selesai) -->
      <div id="signature-container" class="md:col-span-2 hidden mt-3 p-4 bg-slate-50 border border-slate-200 rounded-md">
        <label class="block text-xs font-bold text-slate-800 mb-1.5 uppercase tracking-wider">Tanda Tangan Pelapor (Wajib)</label>
        <p class="text-[11px] text-slate-500 mb-3 font-medium">Silakan tanda tangan di dalam kotak di bawah ini sebagai bukti perbaikan telah selesai dan diserahterimakan.</p>
        <div class="border-2 border-dashed border-slate-300 rounded-md bg-white overflow-hidden" style="width: 100%; max-width: 400px;">
          <canvas id="signature-pad" class="w-full h-48 cursor-crosshair touch-none"></canvas>
        </div>
        <button type="button" onclick="clearSignature()" class="mt-2 text-xs text-red-600 hover:text-red-700 font-semibold px-2 py-1 hover:bg-red-50 rounded transition-colors">Kosongkan Tanda Tangan</button>
        <input type="hidden" name="ttd_pelapor" id="ttd_pelapor">
      </div>
    </div>

    <div class="mt-6 flex items-center gap-4 pt-4 border-t border-slate-100">
      <button type="submit" onclick="return saveSignature()" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
        Simpan Status
      </button>
      <p class="text-xs text-slate-400 font-medium">Jam selesai diisi otomatis saat status → Selesai</p>
    </div>
  </form>
  <?php endif; ?>
  <?php endif; // end of !$isDataLengkap ?>
  </div>
<?php endif; ?>

<!-- Modal Edit Detail (Admin/Teknisi) -->
<div id="modal-edit-detail" class="fixed inset-0 z-[60] hidden overflow-y-auto">
  <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('modal-edit-detail').classList.add('hidden')"></div>
  <div class="flex min-h-full items-center justify-center p-4">
    <div class="relative w-full max-w-lg transform rounded-lg bg-white p-6 shadow-2xl transition-all border border-slate-200">
      <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
        <h3 class="text-lg font-bold text-slate-900 tracking-tight">Lengkapi Data Laporan</h3>
        <button onclick="document.getElementById('modal-edit-detail').classList.add('hidden')" class="text-slate-400 hover:text-red-500 bg-slate-50 hover:bg-red-50 p-1.5 rounded-md transition-colors border border-slate-200 hover:border-red-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <form action="/ipsrs/lk/<?= esc($id) ?>/detail" method="POST">
        <?= csrf_field() ?>
        
        <div class="space-y-4">
          <!-- Kode -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kode Pekerjaan <span class="text-red-500">*</span></label>
            <select name="kode" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm appearance-none">
              <option value="">-- Pilih Kode --</option>
              <?php foreach (($kodeKerusakan ?? []) as $kk): ?>
              <option value="<?= esc($kk['kode'] ?? '') ?>" <?= ($lk['kode'] ?? '') === ($kk['kode'] ?? '') ? 'selected' : '' ?>>
                <?= esc(($kk['kode'] ?? '').' — '.($kk['nama'] ?? '')) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <!-- Aset -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Hubungkan ke Aset</label>
            <select name="id_aset" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm appearance-none">
              <option value="">-- Pilih Aset dari Database --</option>
              <?php foreach (($aset ?? []) as $a): ?>
              <option value="<?= esc($a['id'] ?? '') ?>" <?= ($lk['id_aset_series'] ?? '') == ($a['id'] ?? '') ? 'selected' : '' ?>>
                <?= esc(($a['nomor_aset'] ?? '') . ' - ' . ($a['nama'] ?? '')) ?> (<?= esc(($a['ruangan'] ?? '') . ' / ' . ($a['gedung'] ?? '')) ?>)
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Nama Aset Manual -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Aset (Manual)</label>
            <input type="text" name="nama_aset" value="<?= esc($lk['nama_aset'] ?? '') ?>" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
          </div>
          
          <!-- Lokasi -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Lokasi</label>
          <select name="lokasi" required
                  class="select2 w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
            <option value="">-- Pilih Unit / Lokasi --</option>
            <?php foreach (getStandardUnits() as $u): ?>
              <option value="<?= esc($u) ?>" <?= ($lk['lokasi'] ?? '') === $u ? 'selected' : '' ?>><?= esc($u) ?></option>
            <?php endforeach; ?>
          </select>
          </div>
          
          <!-- Update Aset Checkbox -->
          <div class="p-3 bg-slate-50 border border-slate-200 rounded-md">
            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
              <input type="checkbox" name="update_lokasi_aset" value="1" class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
              Perbarui lokasi master aset sesuai lokasi ini
            </label>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
          <button type="button" onclick="document.getElementById('modal-edit-detail').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-md transition-colors shadow-sm">Batal</button>
          <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors shadow-sm">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
  let signaturePad = null;

  function toggleSignature() {
    const statusSelect = document.getElementById('status_baru');
    const sigContainer = document.getElementById('signature-container');
    
    if (statusSelect && statusSelect.value === 'Selesai') {
      sigContainer.classList.remove('hidden');
      if (!signaturePad) {
        const canvas = document.getElementById('signature-pad');
        // Fix for HDPI screens
        const ratio =  Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        
        signaturePad = new SignaturePad(canvas, {
          backgroundColor: 'rgb(255, 255, 255)'
        });
      }
    } else {
      if (sigContainer) sigContainer.classList.add('hidden');
    }
  }

  function clearSignature() {
    if (signaturePad) {
      signaturePad.clear();
    }
  }

  function saveSignature() {
    const statusSelect = document.getElementById('status_baru');
    
    if (statusSelect && statusSelect.value === 'Selesai') {
      if (signaturePad && signaturePad.isEmpty()) {
        Swal.fire({ icon: 'error', title: 'Oops...', text: 'Tanda Tangan Pelapor wajib diisi jika status Selesai!' });
        return false;
      }
      
      if (signaturePad) {
        const dataUrl = signaturePad.toDataURL('image/png');
        document.getElementById('ttd_pelapor').value = dataUrl;
      }
    }
    return true;
  }
</script>



