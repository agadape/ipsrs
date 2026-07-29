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
    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
      <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <select name="id_aset" id="id_aset" required onchange="updateLokasiSaatIni()"
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Aset --</option>
          <?php foreach (($aset ?? []) as $a): ?>
          <option value="<?= esc($a['id'] ?? '') ?>" data-lokasi="<?= esc($a['lokasi'] ?? '-') ?>">
            <?= esc(($a['nomor_aset'] ?? $a['nomor_seri'] ?? '').' - '.($a['nama'] ?? '')) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Lokasi Saat Ini (Read-only) -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi Saat Ini</label>
        <input type="text" id="lokasi_saat_ini" readonly
               placeholder="Pilih aset terlebih dahulu..."
               class="w-full px-3 py-2.5 text-sm bg-gray-100 text-gray-500 border-0 rounded-xl focus:outline-none cursor-not-allowed">
      </div>

      <!-- Jenis Mutasi -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Mutasi <span class="text-red-500">*</span></label>
        <select name="jenis_mutasi" id="jenis_mutasi" required onchange="toggleLokasiTujuan()"
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Jenis Mutasi --</option>
          <option value="Pindah Ruangan">Pindah Ruangan (Tetap Aktif)</option>
          <option value="Simpan ke Gudang">Simpan ke Gudang (Non-Aktif)</option>
          <option value="Jadikan Kanibal">Jadikan Kanibal (Suku Cadang)</option>
          <option value="Dibuang">Dibuang / Rusak Total</option>
        </select>
      </div>

      <!-- Lokasi Tujuan -->
      <div id="lokasi_tujuan_container" class="hidden">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi Tujuan <span class="text-red-500">*</span></label>
        <input type="text" name="lokasi_tujuan" id="lokasi_tujuan"
               placeholder="Gedung / Ruangan tujuan"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Petugas -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Petugas <span class="text-red-500">*</span></label>
        <select name="petugas" required
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Petugas --</option>
          <?php foreach ($users ?? [] as $u): ?>
          <option value="<?= esc($u['nama_lengkap']) ?>"><?= esc($u['nama_lengkap']) ?> (<?= esc($u['role']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Tanggal -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Catatan -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
        <input type="text" name="catatan"
               placeholder="Catatan tambahan (opsional)"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit"
              class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
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
     class="px-4 py-1.5 rounded-xl text-sm font-medium transition-colors <?= $active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' ?>">
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

<script>
function toggleLokasiTujuan() {
  const jm = document.getElementById('jenis_mutasi').value;
  const ltc = document.getElementById('lokasi_tujuan_container');
  const lti = document.getElementById('lokasi_tujuan');

  if (jm === 'Pindah Ruangan') {
    ltc.classList.remove('hidden');
    lti.setAttribute('required', 'required');
  } else {
    ltc.classList.add('hidden');
    lti.removeAttribute('required');
    lti.value = '';
  }
}

function updateLokasiSaatIni() {
  const select = document.getElementById('id_aset');
  const locationInput = document.getElementById('lokasi_saat_ini');
  if (select.selectedIndex > 0) {
    const opt = select.options[select.selectedIndex];
    locationInput.value = opt.getAttribute('data-lokasi') || '-';
  } else {
    locationInput.value = '';
  }
}
</script>
