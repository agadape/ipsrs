<?php
$jid          = $jadwal['id'] ?? '';
$allTemplate  = $allTemplate ?? [];
$kategoriList = $kategoriList ?? [];
?>

<!-- Page Header -->
<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs/preventif"
     class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 hover:border-gray-200 transition-colors text-gray-500 hover:text-gray-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
  </a>
  <div>
    <h1 class="text-xl font-bold text-gray-800">Lembar Kerja Preventif</h1>
    <p class="text-sm font-medium text-teal-600 mt-0.5"><?= esc($jadwal['aset'] ?? $jadwal['nama_aset'] ?? '-') ?></p>
  </div>
</div>

<!-- Jadwal Info Card -->
<div class="card p-5 mb-6">
  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-3">
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Aset</p>
      <p class="text-sm font-semibold text-gray-800"><?= esc($jadwal['aset'] ?? $jadwal['nama_aset'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Lokasi</p>
      <p class="text-sm font-medium text-gray-700"><?= esc($jadwal['lokasi'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Teknisi</p>
      <p class="text-sm font-medium text-gray-700"><?= esc($jadwal['teknisi'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tanggal &amp; Jam</p>
      <p class="text-sm font-medium text-gray-700">
        <?= tgl($jadwal['tanggal']) ?>
        <?= !empty($jadwal['jam']) ? ' — '.esc($jadwal['jam']) : '' ?>
      </p>
    </div>
  </div>
</div>

<?php if (empty($kategoriList)): ?>
<div class="card p-6 border-l-4 border-amber-400">
  <p class="text-sm text-amber-800">
    ⚠️ Template checklist belum tersedia. Import berkas <code class="font-mono">app\Database\igrations\2026-07-01-100000_full_mysql_schema.sql</code> ke database MySQL terlebih dahulu.
  </p>
</div>
<?php else: ?>

<form method="POST" action="/ipsrs/preventif/lkp/<?= esc($jid) ?>">
  <?= csrf_field() ?>

  <!-- Pilih Kategori Alat -->
  <div class="card p-6 mb-6">
    <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-3 border-b border-gray-100">Jenis Alat yang Diperiksa</h2>
    <div class="max-w-sm">
      <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori Alat <span class="text-red-500">*</span></label>
      <input type="text" name="kategori" id="kategori-select" required
             list="kategori-datalist" placeholder="Pilih atau ketik custom kategori..."
             class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
      <datalist id="kategori-datalist">
        <?php foreach ($kategoriList as $k): ?>
        <option value="<?= esc($k) ?>"></option>
        <?php endforeach; ?>
      </datalist>
      <p class="text-xs text-gray-400 mt-1.5">Pilih dari template, atau ketik bebas untuk form custom.</p>
    </div>
  </div>

  <!-- Checklist (dinamis) -->
  <div id="checklist-area" class="hidden">
    <div class="card p-6 mb-6">
      <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-700">Checklist Pemeriksaan</h2>
        <button type="button" onclick="addCustomRow()" class="text-xs font-semibold text-teal-600 hover:text-teal-800 bg-teal-50 px-3 py-1.5 rounded-lg transition-colors">
          + Tambah Baris
        </button>
      </div>
      
      <!-- Checklist modern flex grid -->
      <div id="checklist-rows" class="space-y-3 mt-4">
        <!-- Rows injected here -->
      </div>
      
      <!-- Tambah Baris Actions -->
      <div class="mt-6 pt-5 border-t border-dashed border-gray-200 flex flex-wrap gap-3 hidden" id="add-buttons">
        <span class="text-xs font-medium text-gray-400 flex items-center mr-2">Tambah Item:</span>
        <button type="button" onclick="addCustomRow('Inspeksi')" class="flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-semibold rounded-lg transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Inspeksi (Pilihan)
        </button>
        <button type="button" onclick="addCustomRow('Pengukuran')" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
          Pengukuran (Angka)
        </button>
        <button type="button" onclick="addCustomRow('Teks')" class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold rounded-lg transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          Input Teks Bebas
        </button>
      </div>
    </div>

    <!-- Kesimpulan -->
    <div class="card p-6 mb-5">
      <h2 class="text-sm font-semibold text-gray-700 mb-5 pb-3 border-b border-gray-100">Kesimpulan</h2>
      <div class="space-y-5">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-2.5">Hasil Pemeriksaan <span class="text-red-500">*</span></label>
          <div class="flex flex-wrap gap-3">
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="radio" name="hasil_pemeriksaan" value="Siap Pakai" required
                     class="w-4 h-4 text-teal-600 focus:ring-teal-400/50 cursor-pointer">
              <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Siap Pakai</span>
              <span class="badge bg-emerald-100 text-emerald-700">Normal</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="radio" name="hasil_pemeriksaan" value="Perlu Perbaikan" required
                     class="w-4 h-4 text-teal-600 focus:ring-teal-400/50 cursor-pointer">
              <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Perlu Perbaikan</span>
              <span class="badge bg-amber-100 text-amber-700">Tindak Lanjut</span>
            </label>
          </div>
          <p class="text-xs text-amber-700 mt-2">⚠️ Hasil "Perlu Perbaikan" akan otomatis membuat LK kuratif baru.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Teknisi</label>
            <input type="text" value="<?= esc($jadwal['teknisi'] ?? session('user_name') ?? '') ?>" readonly
                   class="w-full px-3 py-2.5 text-sm bg-gray-100 border-0 rounded-xl text-gray-600">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pengguna / User <span class="text-red-500">*</span></label>
            <input type="text" name="nama_user_ttd" value="<?= esc(old('nama_user_ttd') ?? '') ?>" required
                   placeholder="Nama perwakilan unit yang menandatangani"
                   class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50">
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan Tambahan</label>
          <textarea name="catatan" rows="3"
                    placeholder="Catatan khusus, rekomendasi perbaikan, atau kondisi yang perlu diperhatikan..."
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400/50 resize-none"><?= esc(old('catatan') ?? '') ?></textarea>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit"
              class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
        Simpan LKP
      </button>
      <a href="/ipsrs/preventif" class="px-5 py-2.5 text-sm text-gray-500 hover:text-gray-700 bg-white rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
        Batal
      </a>
    </div>
  </div>
</form>

<script>
  const TEMPLATE = <?= json_encode($allTemplate, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  const sel      = document.getElementById('kategori-select');
  const area     = document.getElementById('checklist-area');
  const tbody    = document.getElementById('checklist-rows');
  let rowIdx     = 0;

  function updateInputUI(selectElem, i, prefilledValue = '', prefilledSatuan = '') {
    const container = selectElem.closest('.row-card').querySelector('.dynamic-input-container');
    const type = selectElem.value;
    
    let html = '';
    if (type === 'Inspeksi' || type === 'Service') {
        const opts = type === 'Inspeksi' ? ['Baik','Tidak'] : ['Ya','Tidak'];
        const val1 = opts[0];
        const val2 = opts[1];
        const isOpt2 = (prefilledValue === val2);
        
        html = `
        <div class="flex items-center bg-gray-100 p-1 rounded-xl w-full">
          <label class="flex-1 text-center cursor-pointer relative group">
            <input type="radio" name="items[${i}][hasil]" value="${val1}" class="peer sr-only" required ${!isOpt2 && prefilledValue ? 'checked' : ''}>
            <div class="px-2 py-1.5 text-[11px] font-bold tracking-wide uppercase text-gray-500 rounded-lg peer-checked:bg-white peer-checked:text-teal-600 peer-checked:shadow-sm transition-all">${val1}</div>
          </label>
          <label class="flex-1 text-center cursor-pointer relative group">
            <input type="radio" name="items[${i}][hasil]" value="${val2}" class="peer sr-only" required ${isOpt2 ? 'checked' : ''}>
            <div class="px-2 py-1.5 text-[11px] font-bold tracking-wide uppercase text-gray-500 rounded-lg peer-checked:bg-white peer-checked:text-red-500 peer-checked:shadow-sm transition-all">${val2}</div>
          </label>
        </div>`;
    } else if (type === 'Pengukuran') {
        html = `
        <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-teal-400/50 focus-within:border-indigo-400 transition-all w-full">
           <input type="number" step="any" name="items[${i}][hasil]" value="${prefilledValue}" placeholder="Angka..." class="w-full px-3 py-1.5 bg-transparent text-sm font-medium outline-none text-gray-800" required>
           <input type="text" name="items[${i}][satuan]" value="${prefilledSatuan}" placeholder="Satuan" class="w-16 px-2 py-1.5 bg-gray-100 text-xs font-semibold text-gray-500 outline-none border-l border-gray-200">
        </div>`;
    } else {
        html = `
        <input type="text" name="items[${i}][hasil]" value="${prefilledValue}" placeholder="Teks hasil observasi..." class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-teal-400/50 focus:border-indigo-400 transition-all text-gray-800" required>
        `;
    }
    container.innerHTML = html;
  }

  function createRow(data = {}) {
    const i = rowIdx++;
    const tr = document.createElement('div');
    tr.className = 'row-card group flex flex-col md:flex-row items-start md:items-center gap-3 p-3 bg-white border border-gray-100 rounded-2xl hover:border-teal-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300';
    tr.innerHTML = `
      <div class="w-full md:w-36 shrink-0 relative">
        <input type="hidden" name="items[${i}][no_item]" value="${i}">
        <select name="items[${i}][jenis]" onchange="updateInputUI(this, ${i})" class="w-full px-3 py-2 bg-gray-50 border-0 hover:bg-gray-100 focus:bg-white focus:ring-2 focus:ring-teal-400/50 rounded-xl text-[11px] font-bold uppercase tracking-wider text-gray-600 appearance-none cursor-pointer transition-colors">
          <option value="Inspeksi" ${data.jenis === 'Inspeksi' ? 'selected' : ''}>Inspeksi</option>
          <option value="Service" ${data.jenis === 'Service' ? 'selected' : ''}>Service</option>
          <option value="Pengukuran" ${data.jenis === 'Pengukuran' ? 'selected' : ''}>Pengukuran</option>
          <option value="Teks" ${data.jenis === 'Teks' || (!data.jenis) ? 'selected' : ''}>Teks Bebas</option>
        </select>
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
        </div>
      </div>
      
      <div class="w-full md:flex-1 relative">
        <input type="text" name="items[${i}][komponen]" value="${(data.komponen || '').replace(/"/g,'&quot;')}" required placeholder="Apa yang diperiksa? (Misal: Kabel Power)" class="w-full px-1 py-1.5 bg-transparent border-b-2 border-gray-100 hover:border-gray-200 focus:border-indigo-400 focus:bg-gray-50 focus:px-3 focus:rounded-t-lg text-sm transition-all outline-none font-semibold text-gray-800 placeholder-gray-300">
      </div>
      
      <div class="w-full md:w-48 shrink-0 dynamic-input-container">
        <!-- Rendered by JS -->
      </div>
      
      <div class="w-full md:w-48 shrink-0">
        <input type="text" name="items[${i}][ket]" value="${(data.ket || '').replace(/"/g,'&quot;')}" placeholder="Catatan (Opsional)" class="w-full px-3 py-1.5 bg-gray-50 border border-transparent focus:bg-white focus:border-gray-300 rounded-xl text-xs font-medium text-gray-600 outline-none transition-all placeholder-gray-400">
      </div>
      
      <div class="shrink-0 flex justify-end w-full md:w-auto">
        <button type="button" onclick="this.closest('.row-card').remove()" class="text-gray-300 hover:text-red-500 hover:bg-red-50 p-2 rounded-xl transition-all" title="Hapus Baris">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
      </div>
    `;
    tbody.appendChild(tr);
    updateInputUI(tr.querySelector('select'), i, data.hasil || '', data.satuan || '');
  }

  function addCustomRow(type = 'Inspeksi') {
    createRow({ jenis: type });
  }

  function build(kategori) {
    tbody.innerHTML = '';
    if (!kategori) { 
      area.classList.add('hidden'); 
      document.getElementById('add-buttons').classList.add('hidden');
      return; 
    }
    
    const items = TEMPLATE.filter(t => t.kategori === kategori);
    items.forEach(t => {
      createRow({
        jenis: t.jenis_item,
        komponen: t.nama_komponen,
        satuan: t.satuan
      });
    });
    
    area.classList.remove('hidden');
    document.getElementById('add-buttons').classList.remove('hidden');
  }

  sel.addEventListener('change', e => build(e.target.value));
  sel.addEventListener('input', e => {
    if (!TEMPLATE.some(t => t.kategori === e.target.value)) build(e.target.value);
  });
</script>

<?php endif; ?>
