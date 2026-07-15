<?php
$id     = $lk['id'] ?? '';
$status = $lk['status'] ?? '';

$statusSteps = ['Laporan Masuk', 'Didisposisi', 'Survei', 'Dalam Perbaikan', 'Selesai'];
$currentStep = array_search($status, $statusSteps);
if (in_array($status, ['Menunggu Suku Cadang', 'Menunggu Vendor'])) $currentStep = 3;
if ($currentStep === false) $currentStep = 0;

$sBadge = status_lk_badge($status);

$canAssign   = in_array($status, ['Laporan Masuk', 'Didisposisi']);
$canProgress = in_array($status, ['Survei', 'Dalam Perbaikan', 'Menunggu Suku Cadang', 'Menunggu Vendor']);
$isSelesai   = $status === 'Selesai';
$showSC      = !in_array($status, ['Laporan Masuk', 'Didisposisi']);

$prosesLabels = ['I' => 'Proses I — Perbaikan Langsung', 'II' => 'Proses II — Pakai Suku Cadang', 'III' => 'Proses III — Vendor Eksternal'];
?>

<!-- Back -->
<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs/lk"
     class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 hover:border-gray-200 transition-colors text-gray-500 hover:text-gray-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <h1 class="text-xl font-bold text-gray-800">Detail Laporan Kerusakan</h1>
</div>

<!-- LK Header Card -->
<div class="card p-6 mb-6">
  <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
    <div>
      <div class="flex items-center gap-3 flex-wrap mb-2">
        <span class="font-mono text-lg font-bold text-indigo-600"><?= esc($lk['no_order'] ?? '-') ?></span>
        <span class="<?= $sBadge ?>"><?= esc($status) ?></span>
        <span class="<?= kode_badge($lk['kode'] ?? '') ?>"><?= esc($lk['kode'] ?? '-') ?></span>
        <?php if (!empty($lk['proses'])): ?>
        <span class="badge bg-indigo-50 text-indigo-600">Proses <?= esc($lk['proses']) ?></span>
        <?php endif; ?>
      </div>
      <p class="text-gray-500 text-sm"><?= tgl($lk['tanggal'], 'd F Y') ?>
        <?= !empty($lk['jam_laporan']) ? ' · ' . esc($lk['jam_laporan']) : '' ?>
      </p>
    </div>
    <?php if (!$isSelesai): ?>
    <a href="/ipsrs/lk/baru"
       class="flex items-center gap-2 text-xs font-medium px-3 py-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors">
      + LK Baru
    </a>
    <?php endif; ?>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-4">
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Pelapor</p>
      <p class="text-sm font-medium text-gray-800"><?= esc($lk['pelapor'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Unit Pelapor</p>
      <p class="text-sm font-medium text-gray-800"><?= esc($lk['unit_pelapor'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Lokasi</p>
      <p class="text-sm font-medium text-gray-800"><?= esc($lk['lokasi'] ?? '-') ?></p>
    </div>
    <div class="col-span-2 md:col-span-3">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Keluhan</p>
      <p class="text-sm text-gray-800 leading-relaxed"><?= esc($lk['keluhan'] ?? '-') ?></p>
    </div>
    <?php if (!empty($lk['id_aset']) || !empty($lk['nama_aset'])): ?>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Aset Terkait</p>
      <?php if (!empty($lk['id_aset'])): ?>
      <a href="/ipsrs/aset/<?= esc($lk['id_aset']) ?>" class="text-sm font-medium text-indigo-600 hover:underline">
        <?= esc($lk['nama_aset'] ?? $lk['id_aset']) ?>
      </a>
      <?php else: ?>
      <p class="text-sm font-medium text-gray-800"><?= esc($lk['nama_aset']) ?></p>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Status Timeline -->
<div class="card p-6 mb-6">
  <h2 class="text-sm font-semibold text-gray-700 mb-5">Alur Status</h2>
  <div class="flex items-center gap-0 overflow-x-auto pb-1">
    <?php foreach ($statusSteps as $i => $step): ?>
    <?php $done = $i < $currentStep; $current = $i === $currentStep; ?>
    <div class="flex items-center gap-0 shrink-0">
      <div class="flex flex-col items-center">
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
          <?= $done ? 'bg-indigo-600 border-indigo-600 text-white' : ($current ? 'bg-white border-indigo-600 text-indigo-600' : 'bg-white border-gray-200 text-gray-400') ?>">
          <?php if ($done): ?>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
          <?php else: ?><?= $i + 1 ?><?php endif; ?>
        </div>
        <span class="mt-1.5 text-[10px] font-medium text-center leading-tight max-w-[70px]
          <?= $current ? 'text-indigo-600' : ($done ? 'text-gray-500' : 'text-gray-300') ?>">
          <?= esc($step) ?>
        </span>
      </div>
      <?php if ($i < count($statusSteps) - 1): ?>
      <div class="w-12 h-0.5 -mt-4 <?= $done ? 'bg-indigo-400' : 'bg-gray-200' ?>"></div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- ── Completion Details (only when Selesai) ────────────────────────── -->
<?php if ($isSelesai): ?>
<div class="card p-6 mb-6 border border-emerald-100">
  <div class="flex items-center gap-2 mb-5">
    <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
      <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <h2 class="text-sm font-semibold text-gray-700">Hasil Penanganan</h2>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-4 mb-4">
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Teknisi</p>
      <p class="text-sm font-semibold text-gray-800"><?= esc($lk['teknisi'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Jenis Proses</p>
      <p class="text-sm font-semibold text-gray-800">
        <?= !empty($lk['proses']) ? 'Proses ' . esc($lk['proses']) : '-' ?>
      </p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Selesai</p>
      <p class="text-sm font-medium text-gray-800">
        <?= tgl($lk['tanggal_selesai']) ?>
        <?= !empty($lk['jam_selesai']) ? ' ' . esc($lk['jam_selesai']) : '' ?>
      </p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Down Time</p>
      <p class="text-sm font-medium text-gray-800">
        <?= isset($lk['down_time']) && $lk['down_time'] !== null ? (int)$lk['down_time'] . ' menit' : '-' ?>
      </p>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Response Time</p>
      <?php if (isset($lk['response_time']) && $lk['response_time'] !== null): ?>
      <?php $rt = (int)$lk['response_time']; ?>
      <div class="flex items-center gap-2">
        <span class="text-sm font-bold <?= $rt <= 15 ? 'text-emerald-600' : 'text-red-600' ?>"><?= $rt ?> menit</span>
        <span class="badge <?= $rt <= 15 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' ?> text-[10px]">
          <?= $rt <= 15 ? 'SLA Terpenuhi ≤15 mnt' : 'Melebihi SLA' ?>
        </span>
      </div>
      <?php else: ?><p class="text-sm text-gray-400">-</p><?php endif; ?>
    </div>
    <?php if (!empty($lk['tindakan'])): ?>
    <div class="md:col-span-1">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tindakan</p>
      <p class="text-sm text-gray-800 leading-relaxed"><?= esc($lk['tindakan']) ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($lk['ttd_pelapor'])): ?>
    <div class="md:col-span-1 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tanda Tangan Pelapor</p>
      <div class="mt-2 w-32 h-20 border border-gray-200 rounded-lg overflow-hidden bg-white flex items-center justify-center">
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
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-sm font-semibold text-gray-700">Suku Cadang Digunakan</h2>
    <span class="text-xs text-gray-400"><?= count($sukuCadang ?? []) ?> item</span>
  </div>

  <?php if (!empty($sukuCadang)): ?>
  <div class="overflow-x-auto mb-4">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-l-xl">Nama Barang</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Jumlah</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-r-xl">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($sukuCadang as $sc): ?>
        <tr>
          <td class="px-4 py-3 font-medium text-gray-800"><?= esc($sc['nama_barang'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-600"><?= esc($sc['jumlah'] ?? '-') ?> <?= esc($sc['satuan'] ?? '') ?></td>
          <td class="px-4 py-3 text-gray-500"><?= esc($sc['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <p class="text-sm text-gray-400 mb-4">Belum ada suku cadang yang dicatat.</p>
  <?php endif; ?>

  <?php if (!$isSelesai): ?>
  <div class="pt-4 border-t border-gray-100">
    <p class="text-xs font-semibold text-gray-600 mb-3">Tambah Suku Cadang</p>

    <!-- Toggle: Gudang / Kanibal -->
    <div class="flex gap-2 mb-4" id="sc-source-toggle">
      <button type="button" onclick="setScSource('Gudang')"
              id="btn-gudang"
              class="px-4 py-2 text-xs font-semibold rounded-xl transition-colors bg-indigo-600 text-white">
        Dari Gudang
      </button>
      <button type="button" onclick="setScSource('Kanibal')"
              id="btn-kanibal"
              class="px-4 py-2 text-xs font-semibold rounded-xl transition-colors bg-gray-100 text-gray-500 hover:bg-gray-200">
        Kanibal dari Aset Lain
      </button>
    </div>
    <input type="hidden" name="sumber" id="sc-source" value="Gudang">

    <!-- Form Gudang (default) -->
    <form method="POST" action="/ipsrs/lk/<?= esc($id) ?>/suku-cadang" id="form-gudang">
      <?= csrf_field() ?>
      <input type="hidden" name="sumber" value="Gudang">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
        <div class="md:col-span-2">
          <label class="block text-xs font-medium text-gray-500 mb-1">Barang <span class="text-red-500">*</span></label>
          <select name="id_barang" required
                  class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
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
          <label class="block text-xs font-medium text-gray-500 mb-1">Jumlah <span class="text-red-500">*</span></label>
          <input type="number" name="jumlah" min="1" required
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
        <div>
          <button type="submit"
                  class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
            Pakai
          </button>
        </div>
      </div>
    </form>

    <!-- Form Kanibal -->
    <form method="POST" action="/ipsrs/kanibal" id="form-kanibal" class="hidden">
      <?= csrf_field() ?>
      <input type="hidden" name="id_lk" value="<?= esc($id) ?>">
      <input type="hidden" name="no_order_lk" value="<?= esc($lk['no_order'] ?? '') ?>">
      <input type="hidden" name="id_aset_penerima" value="<?= esc($lk['id_aset'] ?? '') ?>">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Aset Donor <span class="text-red-500">*</span></label>
          <select name="id_aset_donor" required id="kanibal-donor"
                  class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none"
                  onchange="loadKomponenDonor(this.value)">
            <option value="">-- Pilih Aset Donor --</option>
            <?php foreach (($aset ?? []) as $a): ?>
            <?php if (($a['status'] ?? '') === 'Kanibal'): ?>
            <option value="<?= esc($a['id'] ?? '') ?>" data-nama="<?= esc($a['nama'] ?? '') ?>">
              <?= esc($a['nomor_aset'] ?? '') ?> — <?= esc($a['nama'] ?? '') ?>
            </option>
            <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Nama Komponen <span class="text-red-500">*</span></label>
          <input type="text" name="nama_komponen" required placeholder="Contoh: Kompresor, Motor Fan, PCB"
                 list="komponen-donor-list"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
          <datalist id="komponen-donor-list"></datalist>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Kondisi Komponen</label>
          <select name="kondisi_komponen"
                  class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
            <option value="Baik">Baik</option>
            <option value="Kurang Baik">Kurang Baik</option>
            <option value="Rusak">Rusak</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Disetujui Oleh</label>
          <input type="text" name="disetujui_oleh" placeholder="Admin / Ka IPSRS"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
        <div class="md:col-span-2">
          <label class="block text-xs font-medium text-gray-500 mb-1">Keterangan</label>
          <input type="text" name="keterangan" placeholder="Catatan kanibal"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
      </div>
      <div class="mt-3">
        <button type="submit"
                class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl transition-colors">
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
      ? 'px-4 py-2 text-xs font-semibold rounded-xl transition-colors bg-indigo-600 text-white'
      : 'px-4 py-2 text-xs font-semibold rounded-xl transition-colors bg-gray-100 text-gray-500 hover:bg-gray-200';
    document.getElementById('btn-kanibal').className = sumber === 'Kanibal'
      ? 'px-4 py-2 text-xs font-semibold rounded-xl transition-colors bg-amber-600 text-white'
      : 'px-4 py-2 text-xs font-semibold rounded-xl transition-colors bg-gray-100 text-gray-500 hover:bg-gray-200';
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
$vendorDetail = $vendorDetail ?? [];
$vendorList   = $vendorList ?? [];
$showVendor   = ($lk['proses'] ?? '') === 'III' || in_array($status, ['Menunggu Vendor']) || !empty($vendorDetail);
?>
<?php if ($showVendor): ?>
<div class="card p-6 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-sm font-semibold text-gray-700">Vendor / Pihak Ke-3 (Proses III)</h2>
    <span class="text-xs text-gray-400"><?= count($vendorDetail) ?> entri</span>
  </div>

  <?php if (!empty($vendorDetail)): ?>
  <div class="overflow-x-auto mb-4">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-l-xl">Vendor</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tgl Kirim</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Estimasi</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tgl Kembali</th>
          <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-r-xl">Keterangan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($vendorDetail as $v): ?>
        <tr>
          <td class="px-4 py-3 font-medium text-gray-800"><?= esc($v['nama_vendor'] ?? '-') ?></td>
          <td class="px-4 py-3 text-gray-600"><?= tgl($v['tanggal_kirim']) ?></td>
          <td class="px-4 py-3 text-gray-600"><?= tgl($v['estimasi_selesai']) ?></td>
          <td class="px-4 py-3 text-gray-600"><?= tgl($v['tanggal_kembali']) ?></td>
          <td class="px-4 py-3 text-gray-500"><?= esc($v['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <p class="text-sm text-gray-400 mb-4">Belum ada data vendor yang dicatat.</p>
  <?php endif; ?>

  <?php if (!$isSelesai): ?>
  <div class="pt-4 border-t border-gray-100">
    <p class="text-xs font-semibold text-gray-600 mb-3">Catat Vendor</p>
    <form method="POST" action="/ipsrs/lk/<?= esc($id) ?>/vendor">
      <?= csrf_field() ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Pilih Vendor</label>
          <select name="id_vendor"
                  class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
            <option value="">-- Pilih Vendor Terdaftar --</option>
            <?php foreach ($vendorList as $vd): ?>
            <option value="<?= esc($vd['id'] ?? '') ?>"><?= esc($vd['nama_vendor'] ?? '') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">atau Vendor Baru</label>
          <input type="text" name="nama_vendor_baru" placeholder="Nama vendor baru"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Kontak <span class="text-gray-400 font-normal">(vendor baru)</span></label>
          <input type="text" name="kontak" placeholder="No. telp / email"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Kirim</label>
          <input type="date" name="tanggal_kirim"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Estimasi Selesai</label>
          <input type="date" name="estimasi_selesai"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Kembali</label>
          <input type="date" name="tanggal_kembali"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
        <div class="md:col-span-2">
          <label class="block text-xs font-medium text-gray-500 mb-1">Keterangan / Ringkasan RAB</label>
          <input type="text" name="keterangan" placeholder="Catatan, biaya, atau ringkasan RAB"
                 class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
      </div>
      <div class="mt-3">
        <button type="submit"
                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
          Simpan Vendor
        </button>
      </div>
    </form>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- ── Update Status Form (hidden when Selesai or if user is pelapor) ──────────────────────── -->
<?php if (!$isSelesai && $authRole !== 'pelapor'): ?>
<div class="card p-6">
  <h2 class="text-sm font-semibold text-gray-700 mb-5">Update Status</h2>

  <?php if ($canAssign): ?>
  <!-- Assign teknisi / disposisi -->
  <form method="POST" action="/ipsrs/lk/<?= esc($id) ?>/status">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Teknisi <span class="text-red-500">*</span></label>
        <select name="teknisi" required
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Teknisi --</option>
          <?php foreach (($teknisiList ?? []) as $t): ?>
          <option value="<?= esc($t['nama_lengkap']) ?>" <?= ($lk['teknisi'] ?? '') === $t['nama_lengkap'] ? 'selected' : '' ?>><?= esc($t['nama_lengkap']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Baru <span class="text-red-500">*</span></label>
        <select name="status_baru" required
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Status --</option>
          <option value="Didisposisi">Didisposisi</option>
          <option value="Survei">Survei</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Cek</label>
        <input type="date" name="tanggal_cek" value="<?= date('Y-m-d') ?>"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Cek</label>
        <input type="time" name="jam_cek" value="<?= date('H:i') ?>"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>
    </div>
    <div class="mt-4">
      <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
        Simpan
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
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Teknisi</label>
        <select name="teknisi"
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih --</option>
          <?php foreach (($teknisiList ?? []) as $t): ?>
          <option value="<?= esc($t['nama_lengkap']) ?>" <?= ($lk['teknisi'] ?? '') === $t['nama_lengkap'] ? 'selected' : '' ?>><?= esc($t['nama_lengkap']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Status Baru -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Baru <span class="text-red-500">*</span></label>
        <select name="status_baru" id="status_baru" required onchange="toggleSignature()"
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Status --</option>
          <option value="Dalam Perbaikan">Dalam Perbaikan</option>
          <option value="Menunggu Suku Cadang">Menunggu Suku Cadang</option>
          <option value="Menunggu Vendor">Menunggu Vendor</option>
          <option value="Selesai">Selesai</option>
        </select>
      </div>

      <!-- Proses I/II/III -->
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-gray-600 mb-2">Jenis Proses</label>
        <div class="flex flex-wrap gap-3">
          <?php foreach ($prosesLabels as $val => $label): ?>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="proses" value="<?= $val ?>"
                   <?= ($lk['proses'] ?? '') === $val ? 'checked' : '' ?>
                   class="w-4 h-4 text-indigo-600 focus:ring-indigo-400/50">
            <span class="text-sm text-gray-700"><?= $label ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Tindakan -->
      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tindakan yang Dilakukan</label>
        <textarea name="tindakan" rows="3"
                  placeholder="Deskripsikan tindakan perbaikan..."
                  class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 resize-none"><?= esc($lk['tindakan'] ?? '') ?></textarea>
      </div>

      <!-- Tanggal & Jam Selesai -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai" value="<?= date('Y-m-d') ?>"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Selesai</label>
        <input type="time" name="jam_selesai" value="<?= date('H:i') ?>"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Tanda Tangan Pelapor (Hidden by default, shown when Selesai) -->
      <div id="signature-container" class="md:col-span-2 hidden mt-2">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanda Tangan Pelapor (Wajib)</label>
        <p class="text-[11px] text-gray-400 mb-2">Silakan tanda tangan di dalam kotak di bawah ini sebagai bukti perbaikan telah selesai dan diserahterimakan.</p>
        <div class="border-2 border-dashed border-gray-300 rounded-xl bg-white overflow-hidden" style="width: 100%; max-width: 400px;">
          <canvas id="signature-pad" class="w-full h-48 cursor-crosshair touch-none"></canvas>
        </div>
        <button type="button" onclick="clearSignature()" class="mt-2 text-xs text-red-500 hover:text-red-700 font-medium">Kosongkan Tanda Tangan</button>
        <input type="hidden" name="ttd_pelapor" id="ttd_pelapor">
      </div>
    </div>

    <div class="mt-4 flex items-center gap-3">
      <button type="submit" onclick="return saveSignature()" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
        Simpan
      </button>
      <p class="text-xs text-gray-400">Jam selesai diisi otomatis saat status → Selesai</p>
    </div>
  </form>
  <?php endif; ?>
</div>
<?php endif; ?>

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
        alert("Tanda Tangan Pelapor wajib diisi jika status Selesai!");
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
