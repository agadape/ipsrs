<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
  <div class="mb-8 border-b border-slate-200 pb-5">
    <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">SOP & Alur Kerja IPSRS</h1>
    <p class="text-sm text-slate-500 mt-1">Panduan praktis penggunaan sistem.</p>
  </div>

  <div class="space-y-6">

    <!-- Accordion Item 1 -->
    <div class="border border-slate-200 rounded-md bg-white overflow-hidden">
      <div class="px-5 py-4 bg-slate-50/50 border-b border-slate-200">
        <h2 class="text-base font-semibold text-slate-800">1. Alur Laporan Kerusakan (LK)</h2>
        <p class="text-xs text-slate-500 mt-1">Langkah-langkah penanganan tiket kerusakan dari pelapor hingga selesai.</p>
      </div>
      <div class="p-5">
        <div class="relative border-l border-slate-200 ml-3 space-y-6 pb-2">
          
          <div class="relative pl-6">
            <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full bg-slate-300 ring-4 ring-white"></span>
            <p class="text-sm font-semibold text-slate-800">Tiket Dibuat (Pelapor)</p>
            <p class="text-sm text-slate-600 mt-1">Pihak ruangan membuat laporan melalui menu <span class="font-medium text-slate-900">Lap. Kerusakan</span>.</p>
          </div>
          
          <div class="relative pl-6">
            <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full bg-slate-300 ring-4 ring-white"></span>
            <p class="text-sm font-semibold text-slate-800">Disposisi & Tindakan (Teknisi)</p>
            <p class="text-sm text-slate-600 mt-1">Admin mendisposisikan laporan. Teknisi mengubah status menjadi <span class="font-medium text-slate-900">Survei</span> atau <span class="font-medium text-slate-900">Perbaikan</span>, lalu memasukkan suku cadang (jika ada).</p>
          </div>
          
          <div class="relative pl-6">
            <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full bg-slate-800 ring-4 ring-white"></span>
            <p class="text-sm font-semibold text-slate-800">Penyelesaian & Tanda Tangan</p>
            <p class="text-sm text-slate-600 mt-1">Teknisi mengubah status ke <span class="font-medium text-slate-900">Selesai</span>. Sistem akan mewajibkan <strong>Tanda Tangan Digital</strong> dari Pelapor sebagai bukti serah terima.</p>
          </div>

        </div>
      </div>
    </div>

    <!-- Accordion Item 2 -->
    <div class="border border-slate-200 rounded-md bg-white overflow-hidden">
      <div class="px-5 py-4 bg-slate-50/50 border-b border-slate-200">
        <h2 class="text-base font-semibold text-slate-800">2. Prosedur Kanibalisasi & Penghapusan</h2>
        <p class="text-xs text-slate-500 mt-1">Aset tidak bisa dihapus atau dikanibal secara sembarangan.</p>
      </div>
      <div class="p-5">
        <div class="relative border-l border-slate-200 ml-3 space-y-6 pb-2">
          
          <div class="relative pl-6">
            <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full bg-slate-300 ring-4 ring-white"></span>
            <p class="text-sm font-semibold text-slate-800">Ubah Status Fisik</p>
            <p class="text-sm text-slate-600 mt-1">Buka <span class="font-medium text-slate-900">Daftar Aset</span>, edit aset yang bermasalah, lalu ubah status fisiknya menjadi <strong>Rusak Berat</strong>.</p>
          </div>
          
          <div class="relative pl-6">
            <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full bg-slate-300 ring-4 ring-white"></span>
            <p class="text-sm font-semibold text-slate-800">Tombol Aksi Muncul</p>
            <p class="text-sm text-slate-600 mt-1">Setelah berstatus Rusak Berat, tombol <strong>[+ Ambil Komponen]</strong> dan <strong>[Lakukan Penghapusan]</strong> otomatis terbuka di halaman Detail Aset.</p>
          </div>
          
          <div class="relative pl-6">
            <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full bg-slate-800 ring-4 ring-white"></span>
            <p class="text-sm font-semibold text-slate-800">Penyelesaian Dokumen</p>
            <p class="text-sm text-slate-600 mt-1">Kanibalisasi wajib melampirkan nomor Laporan Kerusakan. Penghapusan aset (End of Life) wajib melampirkan Berita Acara (BA).</p>
          </div>

        </div>
      </div>
    </div>

    <!-- Accordion Item 3 -->
    <div class="border border-slate-200 rounded-md bg-white overflow-hidden">
      <div class="px-5 py-4 bg-slate-50/50 border-b border-slate-200">
        <h2 class="text-base font-semibold text-slate-800">3. Peminjaman Alat Bantu</h2>
        <p class="text-xs text-slate-500 mt-1">Proses peminjaman sementara untuk aset portabel (bor, tangga, kipas).</p>
      </div>
      <div class="p-5 text-sm text-slate-600">
        <ol class="list-decimal pl-4 space-y-2">
          <li>Pastikan aset berstatus <strong>Tersedia</strong> (tidak sedang rusak/dipinjam).</li>
          <li>Buka Halaman Detail Aset dan klik tombol <strong>[Pinjamkan]</strong>.</li>
          <li>Setelah aset fisik dikembalikan, buka kembali detail aset dan klik <strong>[Terima Pengembalian]</strong>.</li>
        </ol>
      </div>
    </div>

  </div>
</div>
