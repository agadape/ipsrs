<?php
$filterParam = $filter ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Lembar Preventif</h1>
    <p class="text-sm text-slate-500 mt-1">Jadwal & Riwayat pemeliharaan berkala</p>
  </div>
</div>

<!-- Inline Add Form -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-5">
    <h2 class="text-sm font-semibold text-slate-800">Tambah Jadwal Preventif</h2>
  </div>

  <form method="POST" action="/ipsrs/preventif/tambah">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

      <!-- Aset -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Aset <span class="text-red-500">*</span></label>
        <select name="id_aset" id="id_aset"
                class="select2 w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
          <option value="">-- Pilih Aset --</option>
          <?php foreach (($aset ?? []) as $a): ?>
          <option value="<?= esc($a['id'] ?? '') ?>"
                  data-lokasi="<?= esc($a['lokasi'] ?? '') ?>"
                  data-nama="<?= esc($a['nama'] ?? $a['nama_aset'] ?? '') ?>">
            <?= esc(format_aset_label($a)) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Nama Aset (Hidden, populated by JS) -->
      <input type="hidden" name="aset">

      <!-- Lokasi -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Lokasi</label>
        <input type="text" name="lokasi" id="sel_lokasi" readonly disabled
               placeholder="-- Lokasi Otomatis Terisi --"
               class="w-full px-3 py-2 text-sm bg-slate-50 text-slate-500 border border-slate-200 rounded-md focus:outline-none cursor-not-allowed">
        <input type="hidden" name="lokasi" id="hidden_lokasi">
      </div>

      <!-- Teknisi -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Teknisi <span class="text-red-500">*</span></label>
        <select name="teknisi" required
                class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
          <option value="">-- Pilih Teknisi --</option>
          <?php foreach ($users ?? [] as $u): ?>
          <option value="<?= esc($u['nama_lengkap']) ?>"><?= esc($u['nama_lengkap']) ?> (<?= esc($u['role']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Tanggal -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" required min="<?= date('Y-m-d') ?>" id="preventif_tanggal" onchange="validateTime()"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>

      <!-- Jam -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Jam <span class="text-red-500">*</span></label>
        <input type="time" name="jam" required id="preventif_jam" onchange="validateTime()"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>

      <!-- Keterangan -->
      <div class="lg:col-span-3">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Keterangan</label>
        <input type="text" name="keterangan"
               placeholder="Catatan jadwal (opsional)"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-red-600 shadow-sm transition-colors">
      </div>

    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit"
              class="px-6 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
        Tambah Jadwal
      </button>
    </div>
  </form>
</div>

<!-- Filter Tabs -->
<div class="flex flex-wrap gap-2 mb-4">
  <?php
  $tabs = ['' => 'Semua', 'Belum' => 'Belum', 'Selesai' => 'Selesai', 'Terlambat' => 'Terlambat'];
  foreach ($tabs as $val => $label):
    $active = $filterParam === $val;
  ?>
  <a href="/ipsrs/preventif<?= $val ? '?status='.urlencode($val) : '' ?>"
     class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors border
       <?= $active ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900' ?>">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Jadwal Table -->
<div class="card overflow-hidden">
  <?php if (empty($jadwal)): ?>
  <div class="text-center py-16">
    <p class="text-sm text-slate-500">Tidak ada jadwal ditemukan.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Aset</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Lokasi</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Teknisi</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Jam</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($jadwal as $j): ?>
        <?php
          $jStatus = $j['status'] ?? 'Belum';
          $jDate   = $j['tanggal'] ?? '';
          $isLate  = $jStatus !== 'Selesai' && $jDate < ($today ?? date('Y-m-d'));
          if ($isLate) $jStatus = 'Terlambat';
          $jBadge = match($jStatus) {
            'Selesai'   => 'badge bg-emerald-50 text-emerald-700 border border-emerald-200',
            'Terlambat' => 'badge bg-red-50 text-red-700 border border-red-200',
            'Belum'     => 'badge bg-amber-50 text-amber-700 border border-amber-200',
            default     => 'badge bg-slate-50 text-slate-600 border border-slate-200',
          };
          $jid = $j['id'] ?? '';
        ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 text-slate-700 whitespace-nowrap"><?= tgl($jDate) ?></td>
          <td class="px-4 py-3 font-medium text-slate-900"><?= esc($j['aset'] ?? $j['nama_aset'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($j['lokasi'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($j['teknisi'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($j['jam'] ?? '-') ?></td>
          <td class="px-4 py-3"><span class="<?= $jBadge ?>"><?= esc($j['status'] ?? 'Belum') ?></span></td>
          <td class="px-4 py-3 text-right">
            <div class="flex items-center justify-end gap-2 flex-nowrap">
              <?php if (($j['status'] ?? '') !== 'Selesai'): ?>
              <a href="/ipsrs/preventif/lkp/<?= esc($jid) ?>" title="Isi LKP"
                 class="text-xs font-medium px-2.5 py-1.5 rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
                LKP
              </a>
              <?php else: ?>
              <a href="/ipsrs/preventif/lkp-hasil/<?= esc($jid) ?>" title="Lihat Hasil LKP"
                 class="text-xs font-medium px-2.5 py-1.5 rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
                Hasil
              </a>
              <?php endif; ?>
              <form method="POST" action="/ipsrs/preventif/<?= esc($jid) ?>/hapus" class="inline" onsubmit="confirmFormSubmit(event, this, 'Hapus jadwal preventif ini?');">
                <?= csrf_field() ?>
                <button type="submit" title="Hapus Jadwal"
                        class="p-1.5 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<script>
(function() {
  var selAset = document.querySelector('select[name="id_aset"]');
  var inpNama = document.querySelector('input[name="aset"]');
  var selLokasi = document.getElementById('sel_lokasi');
  var hidLokasi = document.getElementById('hidden_lokasi');

  function updateLokasi() {
    var sel = document.getElementById('id_aset');
    var opt = sel.options[sel.selectedIndex];
    if (opt && opt.value !== "") {
      var asetUnit = opt.getAttribute('data-lokasi');
      if (selLokasi && asetUnit) {
         selLokasi.value = asetUnit;
         hidLokasi.value = asetUnit;
      }
    }
  }

  if (selAset) {
    $(selAset).on('change', function() {
      var $opt = $(this).find(':selected');
      if (inpNama && $opt.val()) {
          inpNama.value = $opt.attr('data-nama') || '';
      }
      updateLokasi();
    });
  }
})();

function validateTime() {
  const d = document.getElementById('preventif_tanggal').value;
  const t = document.getElementById('preventif_jam');
  if (!d || !t.value) return;

  const today = new Date().toISOString().split('T')[0];
  if (d === today) {
    const now = new Date();
    const currentHours = now.getHours().toString().padStart(2, '0');
    const currentMinutes = now.getMinutes().toString().padStart(2, '0');
    const currentTime = `${currentHours}:${currentMinutes}`;
    
    if (t.value < currentTime) {
      alert("Jam tidak boleh kurang dari waktu sekarang untuk hari ini.");
      t.value = currentTime;
    }
  }
}
</script>


