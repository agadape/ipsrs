<?php
$filterParam = $filter ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-800">Lembar Preventif</h1>
    <p class="text-sm text-gray-400 mt-0.5">Jadwal & Riwayat pemeliharaan berkala</p>
  </div>
</div>

<!-- Inline Add Form -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-5">
    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
      <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
      </svg>
    </div>
    <h2 class="text-sm font-semibold text-gray-700">Tambah Lembar Preventif</h2>
  </div>

  <form method="POST" action="/ipsrs/preventif/tambah">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

      <!-- Aset -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aset <span class="text-red-500">*</span></label>
        <select name="id_aset"
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Aset --</option>
          <?php foreach (($aset ?? []) as $a): ?>
          <option value="<?= esc($a['id'] ?? '') ?>"
                  data-lokasi="<?= esc($a['lokasi'] ?? '') ?>">
            <?= esc(($a['nomor_aset'] ?? $a['nomor_seri'] ?? '').' — '.($a['nama'] ?? '')) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Nama Aset -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Aset</label>
        <input type="text" name="aset"
               placeholder="Nama aset (jika tidak ada di list)"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Lokasi -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi</label>
        <input type="text" name="lokasi" readonly
               placeholder="Pilih aset terlebih dahulu..."
               class="w-full px-3 py-2.5 text-sm bg-gray-100 text-gray-500 border-0 rounded-xl focus:outline-none cursor-not-allowed">
      </div>

      <!-- Teknisi -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Teknisi <span class="text-red-500">*</span></label>
        <select name="teknisi" required
                class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Teknisi --</option>
          <?php foreach ($users ?? [] as $u): ?>
          <option value="<?= esc($u['nama_lengkap']) ?>"><?= esc($u['nama_lengkap']) ?> (<?= esc($u['role']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Tanggal -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" required min="<?= date('Y-m-d') ?>" id="preventif_tanggal" onchange="validateTime()"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Jam -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam <span class="text-red-500">*</span></label>
        <input type="time" name="jam" required id="preventif_jam" onchange="validateTime()"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Keterangan -->
      <div class="lg:col-span-3">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
        <input type="text" name="keterangan"
               placeholder="Catatan jadwal (opsional)"
               class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit"
              class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 text-white text-[14px] font-bold rounded-2xl transition-all duration-300">
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
     class="px-5 py-2.5 rounded-xl text-[13px] font-bold transition-all duration-300
       <?= $active ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30' : 'bg-white/80 text-gray-500 hover:bg-white hover:text-gray-800 border border-gray-200' ?>">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Jadwal Table -->
<div class="card overflow-hidden">
  <?php if (empty($jadwal)): ?>
  <div class="text-center py-16">
    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <p class="text-sm text-gray-400">Tidak ada jadwal ditemukan.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50/80 border-b border-gray-200/60">
        <tr>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Tanggal</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Aset</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Lokasi</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Teknisi</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Jam</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($jadwal as $j): ?>
        <?php
          $jStatus = $j['status'] ?? 'Belum';
          $jDate   = $j['tanggal'] ?? '';
          $isLate  = $jStatus !== 'Selesai' && $jDate < ($today ?? date('Y-m-d'));
          if ($isLate) $jStatus = 'Terlambat';
          $jBadge = match($jStatus) {
            'Selesai'   => 'badge bg-emerald-100 text-emerald-700',
            'Terlambat' => 'badge bg-red-100 text-red-600',
            'Belum'     => 'badge bg-amber-100 text-amber-700',
            default     => 'badge bg-gray-100 text-gray-500',
          };
          $jid = $j['id'] ?? '';
        ?>
        <tr class="hover:bg-indigo-50/40 transition-colors group">
          <td class="px-5 py-3.5 text-gray-700"><?= tgl($jDate) ?></td>
          <td class="px-5 py-3.5 font-medium text-gray-800"><?= esc($j['aset'] ?? $j['nama_aset'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($j['lokasi'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($j['teknisi'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-600"><?= esc($j['jam'] ?? '-') ?></td>
          <td class="px-5 py-3.5"><span class="<?= $jBadge ?>"><?= esc($j['status'] ?? 'Belum') ?></span></td>
          <td class="px-5 py-3.5">
            <div class="flex items-center gap-2 flex-wrap">
              <?php if (($j['status'] ?? '') !== 'Selesai'): ?>
              <a href="/ipsrs/preventif/lkp/<?= esc($jid) ?>" title="Isi LKP"
                 class="text-xs font-medium px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors flex items-center gap-1">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                LKP
              </a>
              <?php else: ?>
              <a href="/ipsrs/preventif/lkp-hasil/<?= esc($jid) ?>" title="Lihat Hasil LKP"
                 class="text-xs font-medium px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors flex items-center gap-1">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                Hasil
              </a>
              <form method="POST" action="/ipsrs/preventif/<?= esc($jid) ?>/hapus" class="inline" onsubmit="confirmFormSubmit(event, this, 'Hapus jadwal preventif ini?');">
                <?= csrf_field() ?>
                <button type="submit" title="Hapus Jadwal"
                        class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
              </form>
              <?php endif; ?>
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
  var inpLokasi = document.querySelector('input[name="lokasi"]');
  var inpNama = document.querySelector('input[name="aset"]');
  if (selAset && inpLokasi) {
    selAset.addEventListener('change', function() {
      var opt = selAset.options[selAset.selectedIndex];
      inpLokasi.value = opt ? (opt.getAttribute('data-lokasi') || '-') : '';
      if (inpNama && opt.text && opt.value) inpNama.value = opt.text.split(' — ').slice(1).join(' — ');
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
