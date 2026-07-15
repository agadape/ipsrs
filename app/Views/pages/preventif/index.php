<?php
$filterParam = $filter ?? '';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-100">Lembar Preventif</h1>
    <p class="text-sm text-gray-400 mt-0.5">Jadwal & Riwayat pemeliharaan berkala</p>
  </div>
</div>

<!-- Inline Add Form -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-5">
    <div class="w-8 h-8 rounded-lg bg-[#CCFF00]/10 flex items-center justify-center">
      <svg class="w-4 h-4 text-[#CCFF00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
      </svg>
    </div>
    <h2 class="text-sm font-semibold text-gray-200">Tambah Lembar Preventif</h2>
  </div>

  <form method="POST" action="/ipsrs/preventif/tambah">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

      <!-- Aset -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Aset <span class="text-red-500">*</span></label>
        <select name="id_aset"
                class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Aset --</option>
          <?php foreach (($aset ?? []) as $a): ?>
          <option value="<?= esc($a['id'] ?? '') ?>"
                  data-lokasi="<?= esc($a['lokasi'] ?? '') ?>">
            <?= esc(($a['id'] ?? '').' — '.($a['nama'] ?? '')) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Nama Aset -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Aset</label>
        <input type="text" name="aset"
               placeholder="Nama aset (jika tidak ada di list)"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Lokasi -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Lokasi</label>
        <input type="text" name="lokasi"
               placeholder="Lokasi aset"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Teknisi -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Teknisi <span class="text-red-500">*</span></label>
        <input type="text" name="teknisi" required
               placeholder="Nama teknisi"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Tanggal -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" value="<?= esc($today ?? date('Y-m-d')) ?>" required
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Jam -->
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Jam <span class="text-red-500">*</span></label>
        <input type="time" name="jam" required
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>

      <!-- Keterangan -->
      <div class="lg:col-span-3">
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Keterangan</label>
        <input type="text" name="keterangan"
               placeholder="Catatan jadwal (opsional)"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
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
       <?= $active ? 'bg-[#CCFF00] text-black border-none text-white shadow-md shadow-indigo-500/30' : 'bg-white/80 text-gray-400 hover:bg-white hover:text-gray-800 border border-white/10' ?>">
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
            default     => 'badge bg-[#202532] text-gray-400',
          };
          $jid = $j['id'] ?? '';
        ?>
        <tr class="hover:bg-indigo-50/40 transition-colors group">
          <td class="px-5 py-3.5 text-gray-200"><?= tgl($jDate) ?></td>
          <td class="px-5 py-3.5 font-medium text-gray-100"><?= esc($j['aset'] ?? $j['nama_aset'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-300"><?= esc($j['lokasi'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-300"><?= esc($j['teknisi'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-300"><?= esc($j['jam'] ?? '-') ?></td>
          <td class="px-5 py-3.5"><span class="<?= $jBadge ?>"><?= esc($j['status'] ?? 'Belum') ?></span></td>
          <td class="px-5 py-3.5">
            <div class="flex items-center gap-2 flex-wrap">
              <?php if (($j['status'] ?? '') !== 'Selesai'): ?>
              <form method="POST" action="/ipsrs/preventif/<?= esc($jid) ?>/selesai" class="inline">
                <?= csrf_field() ?>
                <button type="submit"
                        class="text-xs font-medium px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors">
                  Selesai
                </button>
              </form>
              <?php endif; ?>
              <a href="/ipsrs/preventif/lkp/<?= esc($jid) ?>"
                 class="text-xs font-medium px-3 py-1.5 rounded-lg bg-[#CCFF00]/10 text-indigo-700 hover:bg-indigo-100 transition-colors">
                LKP
              </a>
              <?php if (($j['status'] ?? '') === 'Selesai'): ?>
              <a href="/ipsrs/preventif/lkp-hasil/<?= esc($jid) ?>"
                 class="text-xs font-medium px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors">
                Hasil
              </a>
              <?php endif; ?>
              <form method="POST" action="/ipsrs/preventif/<?= esc($jid) ?>/hapus" class="inline">
                <?= csrf_field() ?>
                <button type="submit"
                        onclick="return confirm('Hapus jadwal ini?')"
                        class="text-xs font-medium px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                  Hapus
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
  var inpLokasi = document.querySelector('input[name="lokasi"]');
  var inpNama = document.querySelector('input[name="aset"]');
  if (!selAset) return;
  selAset.addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    if (inpLokasi && opt.dataset.lokasi) inpLokasi.value = opt.dataset.lokasi;
    if (inpNama && opt.text && opt.value) inpNama.value = opt.text.split(' — ').slice(1).join(' — ');
  });
})();
</script>
