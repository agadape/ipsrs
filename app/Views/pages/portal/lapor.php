<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Lapor Kerusakan - IPSRS RSUD YK</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://rsms.me/">
  <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  <!-- Select2 for searchable dropdown -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  
  <style>
    body { 
        font-family: 'Inter', sans-serif; 
        background-color: #f8fafc; 
        color: #0f172a; 
        -webkit-font-smoothing: antialiased; 
    }
    
    /* Clean, premium card without generic soft shadows */
    .premium-card {
        background-color: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
    }

    /* Tactile button */
    .btn-tactile {
        background-color: #dc2626; /* red-600 */
        color: #ffffff;
        font-weight: 600;
        letter-spacing: -0.01em;
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .btn-tactile::after {
        content: '';
        position: absolute;
        inset: 0;
        box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.2);
        border-radius: inherit;
        pointer-events: none;
    }
    .btn-tactile:hover {
        background-color: #b91c1c; /* red-700 */
    }
    .btn-tactile:active {
        transform: scale(0.97);
    }

    /* Form Inputs */
    .input-field {
        width: 100%;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #0f172a;
        font-size: 0.9375rem;
        line-height: 1.5;
        transition: all 0.2s ease;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.01);
    }
    .input-field:focus {
        outline: none;
        background-color: #ffffff;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
    }
    .input-field::placeholder {
        color: #94a3b8;
    }
    
    label { 
        font-size: 0.8125rem; 
        font-weight: 600; 
        color: #475569; 
        margin-bottom: 0.375rem; 
        display: block; 
    }
    
    /* Select2 integration to match Tailwind aesthetic */
    .select2-container--default .select2-selection--single {
        height: auto;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.01);
    }
    .select2-container--default.select2-container--open .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #ef4444;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { 
        height: 100%;
        right: 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        color: #0f172a;
        font-size: 0.9375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8;
    }
    .select2-dropdown {
        border-color: #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .select2-search--dropdown .select2-search__field {
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        padding: 0.5rem;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #fef2f2;
        color: #b91c1c;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #f8fafc;
        color: #0f172a;
    }
  </style>
</head>
<body class="flex flex-col justify-center min-h-screen px-4 py-12 sm:px-6 lg:px-8 selection:bg-red-100 selection:text-red-900">

  <div class="w-full max-w-lg mx-auto">
      
    <!-- Branding Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-red-600 mb-5 ring-4 ring-red-50">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Lapor Kerusakan</h1>
        <p class="mt-2 text-sm text-slate-500 font-medium">Bantu kami menjaga fasilitas RSUD Kota Yogyakarta</p>
    </div>

    <!-- Error Alert -->
    <?php if(session()->getFlashdata('error')): ?>
    <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p class="text-sm font-medium text-red-800"><?= esc(session()->getFlashdata('error')) ?></p>
    </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="premium-card p-6 sm:p-8">
        <form method="POST" action="/lapor" class="space-y-6">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="pelapor">Nama Pelapor <span class="text-red-500">*</span></label>
                    <input type="text" id="pelapor" name="pelapor" value="<?= old('pelapor') ?>" required class="input-field" placeholder="Cth: dr. Andi">
                </div>

                <div>
                    <label for="unit_pelapor">Unit / Ruangan <span class="text-red-500">*</span></label>
                    <input type="text" id="unit_pelapor" name="unit_pelapor" value="<?= old('unit_pelapor') ?>" required class="input-field" placeholder="Cth: IGD">
                </div>
            </div>

            <div>
                <label for="lokasi">Lokasi Kerusakan Saat Ini <span class="text-red-500">*</span></label>
                <input type="text" id="lokasi" name="lokasi" value="<?= old('lokasi') ?>" required class="input-field" placeholder="Cth: Kamar Operasi 2 (Di atas pintu)">
            </div>

            <div>
                <div class="flex items-baseline justify-between mb-1.5">
                    <label for="id_aset_series" class="!mb-0">Aset yang Rusak</label>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Opsional</span>
                </div>
                <select id="id_aset_series" name="id_aset_series" class="input-field select2-aset" style="width:100%;">
                    <option value="">-- Tidak Tahu / Aset Tidak Terdaftar --</option>
                    <?php foreach($aset as $a): ?>
                        <option value="<?= esc($a['id']) ?>" <?= (isset($aset_id) && $aset_id == $a['id']) ? 'selected' : '' ?>><?= esc($a['nomor_aset']) ?> - <?= esc($a['nama'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-slate-500 mt-2">Jika ada stiker aset, bantu kami dengan memilihnya dari daftar.</p>
            </div>

            <div>
                <label for="keluhan">Deskripsi Kerusakan <span class="text-red-500">*</span></label>
                <textarea id="keluhan" name="keluhan" required class="input-field min-h-[120px] resize-y" placeholder="Jelaskan secara detail masalah yang terjadi agar teknisi dapat menyiapkan peralatan yang tepat..."><?= old('keluhan') ?></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-tactile w-full py-3.5 rounded-xl flex justify-center items-center gap-2">
                    Kirim Laporan Kerusakan
                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
  
    <div class="mt-8 text-center">
        <p class="text-[11px] font-medium text-slate-400 tracking-wide uppercase">Sistem Informasi Manajemen IPSRS</p>
    </div>

  </div>

  <script>
    $(document).ready(function() {
        $('.select2-aset').select2({
            placeholder: "-- Pilih Aset Jika Ada --",
            allowClear: true,
            language: {
                noResults: function() {
                    return "Aset tidak ditemukan";
                }
            }
        });
    });
  </script>
</body>
</html>
