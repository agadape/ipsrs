<?php
$alasanFilter = $alasan ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Mutasi Aset</h1>
    <p class="text-sm text-slate-500 mt-1">Riwayat dan pencatatan perpindahan aset fisik</p>
  </div>
</div>

<!-- Inline Form: Catat Perpindahan -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-5 pb-3 border-b border-slate-100">
    <div class="w-8 h-8 rounded-md bg-red-50 border border-red-100 flex items-center justify-center">
      <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
      </svg>
    </div>
    <h2 class="text-sm font-bold text-slate-800">Catat Perpindahan Aset</h2>
  </div>

  <form method="POST" action="/ipsrs/aset/mutasi">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

      <!-- ID Aset -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Aset <span class="text-red-500">*</span></label>
        <select name="id_aset" id="id_aset" required onchange="updateLokasiSaatIni()"
                class="select2 w-full px-4 py-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
          <option value="">-- Pilih Aset Fisik --</option>
          <?php foreach (($aset ?? []) as $a): ?>
            <option value="<?= esc($a['id'] ?? '') ?>" data-lokasi="<?= esc($a['lokasi'] ?? '-') ?>">
              <?= esc(format_aset_label($a)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Lokasi Saat Ini (Read-only) -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Lokasi Saat Ini</label>
        <input type="text" id="lokasi_saat_ini" readonly
               placeholder="Pilih aset terlebih dahulu..."
               class="w-full px-3 py-2 text-sm bg-slate-50 text-slate-500 border border-slate-200 rounded-md focus:outline-none cursor-not-allowed">
      </div>

      <!-- Jenis Mutasi -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Jenis Mutasi <span class="text-red-500">*</span></label>
        <select name="jenis_mutasi" id="jenis_mutasi" required onchange="toggleLokasiTujuan()"
                class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm appearance-none">
          <option value="">-- Pilih Jenis Mutasi --</option>
          <option value="Pindah Ruangan">Pindah Ruangan (Tetap Aktif)</option>
          <option value="Simpan ke Gudang">Simpan ke Gudang (Non-Aktif)</option>
          <option value="Jadikan Kanibal">Jadikan Kanibal (Suku Cadang)</option>
          <option value="Dibuang">Dibuang / Rusak Total</option>
        </select>
      </div>

      <!-- Lokasi Tujuan -->
      <div id="lokasi_tujuan_container" class="hidden">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Lokasi Tujuan <span class="text-red-500">*</span></label>
        <input type="text" name="lokasi_tujuan" id="lokasi_tujuan"
               placeholder="Gedung / Ruangan tujuan"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm">
      </div>

      <!-- Petugas -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Petugas <span class="text-red-500">*</span></label>
        <select name="petugas" required
                class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm appearance-none">
          <option value="">-- Pilih Petugas --</option>
          <?php foreach ($users ?? [] as $u): ?>
          <option value="<?= esc($u['nama_lengkap']) ?>"><?= esc($u['nama_lengkap']) ?> (<?= esc($u['role']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Tanggal -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm">
      </div>

      <!-- Catatan -->
      <div class="lg:col-span-3">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Catatan</label>
        <input type="text" name="catatan"
               placeholder="Catatan tambahan (opsional)"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm">
      </div>

    </div>
    <div class="mt-5 flex justify-end">
      <button type="submit"
              class="px-6 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
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
     class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors border <?= $active ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900' ?>">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Riwayat Table -->
<div class="card overflow-hidden">
  <?php if (empty($riwayat)): ?>
  <div class="text-center py-16">
    <p class="text-sm text-slate-500">Belum ada riwayat mutasi.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Aset</th>
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
          <td class="px-4 py-3 text-slate-600 whitespace-nowrap"><?= tgl($r['tanggal']) ?></td>
          <td class="px-4 py-3 font-medium text-slate-900 group-hover:text-red-700 transition-colors"><?= esc($r['nama_aset'] ?? $r['nama'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($r['dari'] ?? $r['lokasi_asal'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($r['ke'] ?? $r['lokasi_tujuan'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($r['alasan'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($r['petugas'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-500 max-w-[180px] truncate"><?= esc($r['catatan'] ?? '-') ?></td>
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



