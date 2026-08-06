<?php
$jid          = $jadwal['id'] ?? '';
$allTemplate  = $allTemplate ?? [];
$kategoriList = $kategoriList ?? [];
?>

<!-- Page Header -->
<div class="flex items-center gap-3 mb-6">
  <a href="/ipsrs/preventif"
     class="w-8 h-8 flex items-center justify-center rounded-md bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
  </a>
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Lembar Kerja Preventif</h1>
    <p class="text-sm font-medium text-indigo-600 mt-1"><?= esc($jadwal['aset'] ?? $jadwal['nama_aset'] ?? '-') ?></p>
  </div>
</div>

<!-- Jadwal Info Card -->
<div class="card p-6 mb-6">
  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-4">
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Aset</p>
      <p class="text-sm font-semibold text-slate-900"><?= esc($jadwal['aset'] ?? $jadwal['nama_aset'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Lokasi</p>
      <p class="text-sm font-medium text-slate-700"><?= esc($jadwal['lokasi'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Teknisi</p>
      <p class="text-sm font-medium text-slate-700"><?= esc($jadwal['teknisi'] ?? '-') ?></p>
    </div>
    <div>
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal &amp; Jam</p>
      <p class="text-sm font-medium text-slate-700">
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
    <h2 class="text-sm font-bold text-slate-800 mb-4 pb-3 border-b border-slate-200">Jenis Alat yang Diperiksa</h2>
    <div class="max-w-sm">
      <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Kategori Alat <span class="text-red-500">*</span></label>
      <input type="text" name="kategori" id="kategori-select" required
             list="kategori-datalist" placeholder="Pilih atau ketik custom kategori..."
             class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      <datalist id="kategori-datalist">
        <?php foreach ($kategoriList as $k): ?>
        <option value="<?= esc($k) ?>"></option>
        <?php endforeach; ?>
      </datalist>
      <p class="text-xs text-slate-500 mt-1.5">Pilih dari template, atau ketik bebas untuk form custom.</p>
    </div>
  </div>

  <!-- Checklist (dinamis) -->
  <div id="checklist-area" class="hidden">
    <div class="card p-6 mb-6">
      <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
        <h2 class="text-sm font-bold text-slate-800">Checklist Pemeriksaan</h2>
        <button type="button" onclick="addCustomRow()" class="text-xs font-medium text-indigo-700 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md transition-colors border border-indigo-200">
          + Tambah Baris
        </button>
      </div>
      
      <!-- Checklist modern flex grid -->
      <div id="checklist-rows" class="space-y-3 mt-4">
        <!-- Rows injected here -->
      </div>
      
      <!-- Tambah Baris Actions -->
      <div class="mt-6 pt-5 border-t border-dashed border-slate-200 flex flex-wrap gap-3 hidden" id="add-buttons">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider flex items-center mr-2">Tambah Item:</span>
        <button type="button" onclick="addCustomRow('Inspeksi')" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-medium rounded-md transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Inspeksi (Pilihan)
        </button>
        <button type="button" onclick="addCustomRow('Service')" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-medium rounded-md transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Service (Tindakan)
        </button>
        <button type="button" onclick="addCustomRow('Pengukuran')" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-medium rounded-md transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
          Pengukuran (Angka)
        </button>
        <button type="button" onclick="addCustomRow('Teks')" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-medium rounded-md transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          Input Teks Bebas
        </button>
      </div>
    </div>

    <!-- Kesimpulan -->
    <div class="card p-6 mb-5">
      <h2 class="text-sm font-bold text-slate-800 mb-5 pb-3 border-b border-slate-200">Kesimpulan</h2>
      <div class="space-y-5">
        <!-- Lokasi Alat -->
        <div class="mb-5">
          <label class="block text-xs font-semibold text-slate-600 mb-2.5 uppercase tracking-wider">Lokasi Alat Sesuai? <span class="text-red-500">*</span></label>
          <div class="flex flex-wrap gap-4">
            <label class="flex items-center gap-2 cursor-pointer group">
              <input type="radio" name="lokasi_sesuai" value="Sesuai" required
                     class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 cursor-pointer border-slate-300">
              <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Ya, Sesuai</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer group">
              <input type="radio" name="lokasi_sesuai" value="Tidak Sesuai" required
                     class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 cursor-pointer border-slate-300">
              <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Tidak, Berpindah</span>
            </label>
          </div>
        </div>

        <div class="mb-5">
          <label class="block text-xs font-semibold text-slate-600 mb-2.5 uppercase tracking-wider">Hasil Pemeriksaan <span class="text-red-500">*</span></label>
          <div class="flex flex-wrap gap-4">
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="radio" name="hasil_pemeriksaan" value="Siap Pakai" required
                     class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 cursor-pointer border-slate-300">
              <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Siap Pakai</span>
              <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Normal</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="radio" name="hasil_pemeriksaan" value="Perlu Perbaikan" required
                     class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 cursor-pointer border-slate-300">
              <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Perlu Perbaikan</span>
              <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Tindak Lanjut</span>
            </label>
          </div>
          <p class="text-xs text-amber-700 mt-2 font-medium">⚠️ Hasil "Perlu Perbaikan" akan otomatis membuat LK kuratif baru.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Teknisi</label>
            <input type="text" value="<?= esc($jadwal['teknisi'] ?? session('user_name') ?? '') ?>" readonly
                   class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-md text-slate-500">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Pengguna / User <span class="text-red-500">*</span></label>
            <input type="text" name="nama_user_ttd" value="<?= esc(old('nama_user_ttd') ?? '') ?>" required
                   placeholder="Nama perwakilan unit"
                   class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Catatan Tambahan</label>
          <textarea name="catatan" rows="3"
                    placeholder="Catatan khusus, rekomendasi perbaikan, atau kondisi yang perlu diperhatikan..."
                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors resize-none"><?= esc(old('catatan') ?? '') ?></textarea>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3">
      <a href="/ipsrs/preventif" class="px-5 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 bg-white border border-slate-200 rounded-md hover:bg-slate-50 transition-colors shadow-sm">
        Batal
      </a>
      <button type="submit"
              class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
        Simpan LKP
      </button>
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
        <div class="flex items-center bg-slate-100 p-1 rounded-md w-full border border-slate-200">
          <label class="flex-1 text-center cursor-pointer relative group">
            <input type="radio" name="items[${i}][hasil]" value="${val1}" class="peer sr-only" required ${!isOpt2 && prefilledValue ? 'checked' : ''}>
            <div class="px-2 py-1.5 text-[11px] font-bold tracking-wide uppercase text-slate-500 rounded peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm transition-all">${val1}</div>
          </label>
          <label class="flex-1 text-center cursor-pointer relative group">
            <input type="radio" name="items[${i}][hasil]" value="${val2}" class="peer sr-only" required ${isOpt2 ? 'checked' : ''}>
            <div class="px-2 py-1.5 text-[11px] font-bold tracking-wide uppercase text-slate-500 rounded peer-checked:bg-white peer-checked:text-red-600 peer-checked:shadow-sm transition-all">${val2}</div>
          </label>
        </div>`;
    } else if (type === 'Pengukuran') {
        html = `
        <div class="flex items-center bg-white border border-slate-200 rounded-md overflow-hidden focus-within:ring-1 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-all w-full shadow-sm">
           <input type="number" step="any" name="items[${i}][hasil]" value="${prefilledValue}" placeholder="Angka..." class="w-full px-3 py-1.5 bg-transparent text-sm font-medium outline-none text-slate-900" required>
           <input type="text" name="items[${i}][satuan]" value="${prefilledSatuan}" placeholder="Satuan" class="w-16 px-2 py-1.5 bg-slate-50 text-xs font-medium text-slate-600 outline-none border-l border-slate-200">
        </div>`;
    } else {
        html = `
        <input type="text" name="items[${i}][hasil]" value="${prefilledValue}" placeholder="Teks hasil observasi..." class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-md text-sm font-medium outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-slate-900 shadow-sm" required>
        `;
    }
    container.innerHTML = html;
  }

  function createRow(data = {}) {
    const i = rowIdx++;
    const tr = document.createElement('div');
    tr.className = 'row-card group flex flex-col md:flex-row items-start md:items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-lg hover:border-indigo-200 transition-colors duration-200';
    tr.innerHTML = `
      <div class="w-full md:w-36 shrink-0 relative">
        <input type="hidden" name="items[${i}][no_item]" value="${i}">
        <select name="items[${i}][jenis]" onchange="updateInputUI(this, ${i})" class="w-full px-3 py-2 bg-white border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md text-[11px] font-bold uppercase tracking-wider text-slate-700 appearance-none cursor-pointer transition-colors shadow-sm">
          <option value="Inspeksi" ${data.jenis === 'Inspeksi' ? 'selected' : ''}>Inspeksi</option>
          <option value="Service" ${data.jenis === 'Service' ? 'selected' : ''}>Service</option>
          <option value="Pengukuran" ${data.jenis === 'Pengukuran' ? 'selected' : ''}>Pengukuran</option>
          <option value="Teks" ${data.jenis === 'Teks' || (!data.jenis) ? 'selected' : ''}>Teks Bebas</option>
        </select>
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
      </div>
      
      <div class="w-full md:flex-1 relative">
        <input type="text" name="items[${i}][komponen]" value="${(data.komponen || '').replace(/"/g,'&quot;')}" required placeholder="Apa yang diperiksa? (Misal: Kabel Power)" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-md focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition-all outline-none font-medium text-slate-900 placeholder-slate-400 shadow-sm">
      </div>
      
      <div class="w-full md:w-48 shrink-0 dynamic-input-container">
        <!-- Rendered by JS -->
      </div>
      
      <div class="w-full md:w-48 shrink-0">
        <input type="text" name="items[${i}][ket]" value="${(data.ket || '').replace(/"/g,'&quot;')}" placeholder="Catatan (Opsional)" class="w-full px-3 py-1.5 bg-white border border-slate-200 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-md text-xs font-medium text-slate-600 outline-none transition-all placeholder-slate-400 shadow-sm">
      </div>
      
      <div class="shrink-0 flex justify-end w-full md:w-auto">
        <button type="button" onclick="this.closest('.row-card').remove()" class="text-slate-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded-md transition-colors border border-transparent hover:border-red-200" title="Hapus Baris">
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
