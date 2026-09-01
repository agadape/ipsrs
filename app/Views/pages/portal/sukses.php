<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Laporan Berhasil ?" RSUD Kota Yogyakarta</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://rsms.me/">
  <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
    .card { background-color: #ffffff; border-radius: 24px; box-shadow: 0 20px 60px -15px rgba(0,0,0,0.05), 0 1px 3px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; }
    .btn-secondary { background-color: #f1f5f9; color: #0f172a; transition: all 0.2s; border-radius: 9999px; }
    .btn-secondary:hover { background-color: #e2e8f0; }
  </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-4" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 32px 32px;">

  <div class="card w-full max-w-sm p-8 text-center relative z-10 flex flex-col items-center">
    
    <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mb-5 border-[4px] border-emerald-50">
      <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </div>
    
    <h1 class="text-2xl font-bold text-slate-900 mb-2 tracking-tight">Laporan Terkirim!</h1>
    <p class="text-sm text-slate-500 mb-6 leading-relaxed">
        Terima kasih, laporan kerusakan Anda telah kami terima dan akan segera ditindaklanjuti oleh teknisi IPSRS.
    </p>

    <div class="w-full bg-slate-50 border border-slate-100 rounded-xl p-4 mb-8">
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor Tiket Anda</p>
        <p class="text-xl font-black text-red-700 tracking-wide"><?= esc($order ?? '-') ?></p>
    </div>

    <a href="/lapor" class="btn-secondary w-full py-3.5 font-bold text-[14px] flex justify-center items-center gap-2">
        Buat Laporan Baru
    </a>
  </div>

</body>
</html>
