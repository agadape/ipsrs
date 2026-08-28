
<div class="mb-6">
  <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Panduan & Alur Sistem (SOP)</h1>
  <p class="text-sm text-slate-500 mt-1">Panduan singkat untuk memahami logika dan alur kerja (workflow) CMMS IPSRS.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

  <!-- Alur 1 -->
  <div class="card p-6 border-t-4 border-blue-600">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">1</div>
      <h2 class="text-base font-bold text-slate-800">Alur Pelaporan Kerusakan (LK)</h2>
    </div>
    <ol class="list-decimal pl-5 space-y-2 text-sm text-slate-600">
      <li><strong>Pelapor</strong> membuat Laporan Kerusakan (LK) melalui menu <span class="font-semibold">Lap. Kerusakan</span>.</li>
      <li>Admin IPSRS / Kepala menugaskan (Disposisi) laporan tersebut ke <strong>Teknisi</strong>.</li>
      <li>Teknisi melakukan <span class="font-semibold text-amber-600">Survei</span> atau <span class="font-semibold text-blue-600">Perbaikan</span>. Teknisi bisa menambahkan suku cadang atau vendor jika perlu.</li>
      <li>Setelah selesai, Teknisi mengubah status menjadi <span class="font-semibold text-emerald-600">Selesai</span>. Pada tahap ini, <strong>Pelapor wajib memberikan Tanda Tangan digital</strong> sebagai bukti serah terima perbaikan.</li>
    </ol>
  </div>

  <!-- Alur 2 -->
  <div class="card p-6 border-t-4 border-red-600">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-700 font-bold">2</div>
      <h2 class="text-base font-bold text-slate-800">Alur Aset Rusak & Kanibal</h2>
    </div>
    <p class="text-sm text-slate-600 mb-3">Sistem ini sangat ketat menjaga riwayat aset. Anda tidak bisa tiba-tiba menghapus atau mengkanibal aset sembarangan.</p>
    <ul class="list-disc pl-5 space-y-2 text-sm text-slate-600">
      <li>Untuk mengkanibal atau menghapus aset, aset tersebut <strong>wajib berstatus "Rusak Berat"</strong>.</li>
      <li>Buka <span class="font-semibold">Daftar Aset</span> &rarr; Pilih Aset &rarr; Klik <span class="font-semibold">Edit Aset</span> &rarr; Ubah Status menjadi <strong>Rusak Berat</strong>.</li>
      <li>Setelah statusnya Rusak Berat, saat Anda membuka halaman Detail Aset tersebut, tombol aksi <strong>[+ Ambil Komponen/Kanibal]</strong> dan <strong>[Lakukan Penghapusan]</strong> akan otomatis muncul.</li>
      <li>Pencatatan Kanibal membutuhkan nomor Laporan Kerusakan (LK) yang sedang dikerjakan.</li>
    </ul>
  </div>

  <!-- Alur 3 -->
  <div class="card p-6 border-t-4 border-amber-500">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-bold">3</div>
      <h2 class="text-base font-bold text-slate-800">Alur Peminjaman Aset</h2>
    </div>
    <ul class="list-disc pl-5 space-y-2 text-sm text-slate-600">
      <li>Aset sarana-prasarana IPSRS secara standar berstatus <strong>Tersedia</strong> (menetap dan beroperasi di ruangannya).</li>
      <li>Jika departemen lain ingin meminjam alat bantu (misal: tangga, bor, kipas), buka halaman Detail Aset tersebut lalu klik tombol <strong>[Pinjamkan]</strong>.</li>
      <li>Status aset akan berubah menjadi <span class="font-semibold text-amber-600">Dipinjam</span>.</li>
      <li>Saat dikembalikan, buka lagi halaman detailnya dan klik <strong>[Terima Pengembalian]</strong>. Histori peminjaman dapat dilihat di menu Peminjaman Aset.</li>
    </ul>
  </div>

  <!-- Alur 4 -->
  <div class="card p-6 border-t-4 border-slate-800">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-800 font-bold">4</div>
      <h2 class="text-base font-bold text-slate-800">Penghapusan (End of Life)</h2>
    </div>
    <ul class="list-disc pl-5 space-y-2 text-sm text-slate-600">
      <li>Penghapusan aset berarti aset dikeluarkan dari sirkulasi (End of Life), <strong>namun datanya tidak dihapus dari database</strong> untuk keperluan audit.</li>
      <li>Proses ini <strong>wajib melampirkan Dokumen Berita Acara (BA)</strong>.</li>
      <li>Aset yang dihapuskan akan masuk ke dalam arsip (menu Penghapusan Aset) dan statusnya terkunci menjadi <span class="font-semibold text-slate-800">Dihapuskan</span>.</li>
    </ul>
  </div>

</div>

