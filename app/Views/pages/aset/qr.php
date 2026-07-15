<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Aset — <?= esc($aset['nama'] ?? '') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
      .print-card { box-shadow: none !important; border: 2px solid #000 !important; }
    }
  </style>
</head>
<body class="bg-[#202532] min-h-screen flex flex-col items-center justify-center p-8">

  <!-- Print button -->
  <div class="no-print mb-6 flex gap-3">
    <button onclick="window.print()"
            class="px-5 py-2.5 bg-[#CCFF00] text-black border-none hover:bg-[#B3E600] text-black text-white text-sm font-semibold rounded-xl transition-colors shadow-[0_4px_20px_rgba(0,0,0,0.5)]">
      🖨️ Cetak / Simpan PDF
    </button>
    <a href="/ipsrs/aset/<?= esc($aset['id'] ?? '') ?>"
       class="px-5 py-2.5 bg-[#121620]/60 hover:bg-white/5 text-gray-200 text-sm font-semibold rounded-xl border border-white/10 transition-colors">
      ← Kembali
    </a>
  </div>

  <!-- QR Card (printable) -->
  <div class="print-card bg-[#121620]/60 rounded-2xl shadow-lg p-8 w-72 flex flex-col items-center gap-4">
    <!-- Header -->
    <div class="text-center">
      <p class="text-xs font-bold text-[#CCFF00] uppercase tracking-widest">RSUD IPSRS</p>
      <p class="text-xs text-gray-400 mt-0.5">Scan untuk info aset</p>
    </div>

    <!-- QR -->
    <div id="qrcode" class="p-2 border border-white/10 rounded-xl"></div>

    <!-- Asset info -->
    <div class="text-center border-t border-white/5 pt-4 w-full">
      <p class="text-xs font-mono font-bold text-[#CCFF00]"><?= esc($aset['id'] ?? '') ?></p>
      <p class="text-sm font-bold text-gray-100 mt-1"><?= esc($aset['nama'] ?? '') ?></p>
      <p class="text-xs text-gray-400 mt-0.5"><?= esc($aset['lokasi'] ?? '') ?></p>
    </div>

    <!-- Footer info -->
    <div class="grid grid-cols-2 gap-3 w-full text-center">
      <div class="bg-[#181C25]/80 rounded-lg p-2">
        <p class="text-xs text-gray-400">Jenis</p>
        <p class="text-xs font-semibold text-gray-200"><?= esc($aset['jenis'] ?? '-') ?></p>
      </div>
      <div class="bg-[#181C25]/80 rounded-lg p-2">
        <p class="text-xs text-gray-400">Status</p>
        <p class="text-xs font-semibold text-emerald-600"><?= esc($aset['status'] ?? '-') ?></p>
      </div>
    </div>

    <p class="text-xs text-gray-300 text-center">Dicetak <?= date('d/m/Y') ?></p>
  </div>

  <script>
    <?php
      $qrUrl = base_url('/ipsrs/aset/' . ($aset['id'] ?? '')) . '?via=qr';
    ?>
    var url = '<?= esc($qrUrl) ?>';
    new QRCode(document.getElementById('qrcode'), {
      text: url,
      width: 200,
      height: 200,
      colorDark: '#1e293b',
      colorLight: '#ffffff',
      correctLevel: QRCode.CorrectLevel.M
    });
  </script>
</body>
</html>
