<?php
$alasanFilter = $alasan ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-800">Mutasi Aset</h1>
    <p class="text-sm text-gray-400 mt-0.5">Riwayat dan pencatatan perpindahan aset</p>
  </div>
</div>

<!-- Inline Form: Catat Perpindahan -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-5">
    <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center">
      <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
      </svg>
    </div>
    <h2 class="text-sm font-semibold text-gray-700">Catat Perpindahan Aset</h2>
  </div>

  <form method="POST" action="/ipsrs/aset/mutasi">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

      <!-- ID Aset -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aset <span class="text-red-500">*</span></label>
        <select name="id_aset" required
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50 appearance-none">
          <option value="">-- Pilih Aset --</option>
          <?php foreach (($aset ?? []) as $a): ?>
          <option value="<?= esc($a['id'] ?? '') ?>">
            <?= esc(($a['id_aset'] ?? '').' — '.($a['nama'] ?? '')) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Lokasi Tujuan -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi Tujuan <span class="text-red-500">*</span></label>
        <input type="text" name="lokasi_tujuan" required
               placeholder="Gedung / Ruangan tujuan"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
      </div>

      <!-- Alasan -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alasan <span class="text-red-500">*</span></label>
        <select name="alasan" required
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50 appearance-none">
          <option value="">-- Pilih Alasan --</option>
          <?php foreach (['Pemindahan', 'Perbaikan', 'Pengembalian', 'Lainnya'] as $opt): ?>
          <option value="<?= $opt ?>"><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Status Baru -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Update Status Aset</label>
        <select name="status_baru"
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50 appearance-none">
          <option value="">-- Tetap (Tidak Diubah) --</option>
          <?php foreach (\App\Config\IPSRS::STATUS_ASET as $opt): ?>
          <option value="<?= $opt ?>"><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Petugas -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Petugas <span class="text-red-500">*</span></label>
        <input type="text" name="petugas" required
               placeholder="Nama petugas"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
      </div>

      <!-- Tanggal -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
      </div>

      <!-- Catatan -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
        <input type="text" name="catatan"
               placeholder="Catatan tambahan (opsional)"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
      </div>

    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit"
              class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
        Simpan Mutasi
      </button>
    </div>
  </form>
</div>

<!-- Filter Alasan -->
<div class="flex flex-wrap gap-2 mb-4">
  <?php $alasanOpts = ['' => 'Semua', 'Pemindahan' => 'Pemindahan', 'Perbaikan' => 'Perbaikan', 'Pengembalian' => 'Pengembalian', 'Lainnya' => 'Lainnya']; ?>
  <?php foreach ($alasanOpts as $val => $label): ?>
  <?php $active = $alasanFilter === $val; ?>
  <a href="/ipsrs/aset/mutasi<?= $val ? '?alasan='.urlencode($val) : '' ?>"
     class="px-4 py-1.5 rounded-xl text-sm font-medium transition-colors <?= $active ? 'bg-teal-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' ?>">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Riwayat Table -->
<div class="card overflow-hidden">
  <?php if (empty($riwayat)): ?>
  <div class="text-center py-16">
    <p class="text-sm text-gray-400">Belum ada riwayat mutasi.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Aset</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Dari</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Ke</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Alasan</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Petugas</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Catatan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($riwayat as $r): ?>
        <tr class="hover:bg-gray-50/60 transition-colors">
          <td class="px-5 py-3.5 text-gray-600"><?= tgl($r['tanggal']) ?></td>
          <td class="px-5 py-3.5 font-medium text-gray-800"><?= esc($r['nama_aset'] ?? $r['nama'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($r['dari'] ?? $r['lokasi_asal'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($r['ke'] ?? $r['lokasi_tujuan'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($r['alasan'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($r['petugas'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-500 max-w-[180px] truncate"><?= esc($r['catatan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
