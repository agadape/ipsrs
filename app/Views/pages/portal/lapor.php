<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Lapor Kerusakan Aset ?" RSUD Kota Yogyakarta</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://rsms.me/">
  <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  <!-- Select2 for searchable dropdown -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; -webkit-font-smoothing: antialiased; }
    .card { background-color: #ffffff; border-radius: 24px; box-shadow: 0 20px 60px -15px rgba(0,0,0,0.05), 0 1px 3px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; }
    
    .btn-primary { background-color: #b91c1c; color: #ffffff; box-shadow: 0 4px 14px 0 rgba(185, 28, 28, 0.39); transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); border-radius: 9999px; }
    .btn-primary:hover { background-color: #991b1b; transform: translateY(-1px); }
    .btn-primary:active { transform: scale(0.98); }

    .input-field {
        width: 100%; padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid #e2e8f0; background-color: #f8fafc;
        transition: all 0.2s; font-size: 0.95rem; color: #0f172a;
    }
    .input-field:focus { outline: none; border-color: #b91c1c; background-color: #ffffff; box-shadow: 0 0 0 4px rgba(185, 28, 28, 0.1); }
    
    label { font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.35rem; display: block; }
    
    /* Select2 custom styling */
    .select2-container--default .select2-selection--single {
        height: 48px; border-radius: 12px; border: 1px solid #e2e8f0; background-color: #f8fafc; display: flex; align-items: center; padding-left: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px; }
    .select2-container--default.select2-container--open .select2-selection--single { border-color: #b91c1c; background-color: #ffffff; }
  </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-4 selection:bg-red-100 selection:text-red-950" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 32px 32px;">

  <!-- Header -->
  <div class="w-full max-w-md flex flex-col items-center mb-6 mt-4">
    <div class="w-12 h-12 rounded-lg bg-red-700 flex items-center justify-center mb-3 shadow-lg shadow-red-700/30">
      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Portal Laporan Kerusakan</h2>
    <p class="text-sm text-slate-500 font-medium mt-1">IPSRS RSUD Kota Yogyakarta</p>
  </div>

  <div class="card w-full max-w-md p-6 md:p-8 relative z-10">
    
    <?php if(session()->getFlashdata('error')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p class="text-sm font-medium text-red-800"><?= esc(session()->getFlashdata('error')) ?></p>
    </div>
    <?php endif; ?>

    <form method="POST" action="/lapor" class="flex flex-col gap-5">
        <?= csrf_field() ?>
        
        <div>
            <label for="pelapor">Nama Pelapor <span class="text-red-500">*</span></label>
            <input type="text" id="pelapor" name="pelapor" value="<?= old('pelapor') ?>" required class="input-field" placeholder="Cth: dr. Andi / Perawat Sari">
        </div>

        <div>
            <label for="unit_pelapor">Unit / Ruangan Pelapor <span class="text-red-500">*</span></label>
            <input type="text" id="unit_pelapor" name="unit_pelapor" value="<?= old('unit_pelapor') ?>" required class="input-field" placeholder="Cth: IGD / Poli Gigi">
        </div>

        <div>
            <label for="lokasi">Lokasi Kerusakan Saat Ini <span class="text-red-500">*</span></label>
            <input type="text" id="lokasi" name="lokasi" value="<?= old('lokasi') ?>" required class="input-field" placeholder="Cth: Kamar Operasi 2">
        </div>

        <div>
            <label for="id_aset_series">Aset yang Rusak (Opsional)</label>
            <select id="id_aset_series" name="id_aset_series" class="input-field select2-aset" style="width:100%;">
                <option value="">-- Tidak Tahu / Aset Tidak Terdaftar --</option>
                <?php foreach($aset as $a): ?>
                    <option value="<?= esc($a['id']) ?>" <?= (isset($aset_id) && $aset_id == $a['id']) ? 'selected' : '' ?>><?= esc($a['nomor_aset']) ?> - <?= esc($a['nama_aset']) ?></option>
                <?php endforeach; ?>
            </select>
            <p class="text-xs text-slate-500 mt-1.5">Pilih jika kerusakan terjadi pada aset spesifik yang memiliki label QR.</p>
        </div>

        <div>
            <label for="keluhan">Deskripsi Kerusakan <span class="text-red-500">*</span></label>
            <textarea id="keluhan" name="keluhan" required class="input-field min-h-[100px] resize-y" placeholder="Ceritakan detail kerusakan yang terjadi..."><?= old('keluhan') ?></textarea>
        </div>

        <button type="submit" class="btn-primary w-full py-3.5 font-bold text-[15px] flex justify-center items-center gap-2 mt-2">
            Kirim Laporan Kerusakan
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </button>
    </form>

  </div>
  
  <div class="mt-8 text-center text-xs font-medium text-slate-400">
    <p>Dikembangkan untuk Internal Enterprise</p>
    <p>RSUD Kota Yogyakarta A <?= date('Y') ?></p>
  </div>

  <script>
    $(document).ready(function() {
        $('.select2-aset').select2({
            placeholder: "-- Pilih Aset Jika Ada --",
            allowClear: true
        });
    });
  </script>
</body>
</html>
