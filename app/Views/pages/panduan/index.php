<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
  <div class="mb-10 border-b border-slate-200 pb-6">
    <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Standard Operating Procedure (SOP)</h1>
    <p class="text-sm text-slate-500 mt-2">Dokumentasi alur kerja sistem CMMS IPSRS RSUD.</p>
  </div>

  <div class="space-y-12">
    
    <!-- Section 1 -->
    <section>
      <h2 class="text-lg font-medium text-slate-900 mb-4">1. Alur Pelaporan Kerusakan (LK)</h2>
      <div class="prose prose-sm prose-slate max-w-none text-slate-600">
        <p>Proses perbaikan aset sarana dan prasarana harus melalui pencatatan Laporan Kerusakan. Alur penyelesaiannya adalah sebagai berikut:</p>
        <ol class="list-decimal pl-4 mt-3 space-y-2">
          <li><strong>Pelapor</strong> membuat tiket Laporan Kerusakan (LK) melalui menu <em>Lap. Kerusakan</em>.</li>
          <li><strong>Kepala IPSRS / Admin</strong> melakukan disposisi laporan kepada teknisi yang bertugas.</li>
          <li><strong>Teknisi</strong> memeriksa aset di lapangan (Survei) dan melakukan tindak lanjut perbaikan. Selama proses ini, teknisi dapat mendaftarkan pemakaian suku cadang atau pihak ketiga (vendor) ke dalam sistem.</li>
          <li>Setelah perbaikan selesai, teknisi mengubah status laporan menjadi <strong>Selesai</strong>.</li>
          <li><strong>Penting:</strong> Pada tahap penyelesaian, Pelapor (pihak ruangan) diwajibkan untuk memberikan tanda tangan digital pada sistem sebagai bukti serah terima bahwa aset telah berfungsi kembali.</li>
        </ol>
      </div>
    </section>

    <hr class="border-slate-100">

    <!-- Section 2 -->
    <section>
      <h2 class="text-lg font-medium text-slate-900 mb-4">2. Manajemen Aset Rusak & Kanibalisasi</h2>
      <div class="prose prose-sm prose-slate max-w-none text-slate-600">
        <p>Sistem ini mengunci integritas data aset. Aset tidak dapat dihapus secara permanen atau dikanibal secara sepihak tanpa merubah status fisiknya terlebih dahulu.</p>
        <ul class="list-disc pl-4 mt-3 space-y-2">
          <li>Untuk melakukan aksi <em>Kanibalisasi</em> atau <em>Penghapusan</em>, aset tersebut wajib diubah statusnya menjadi <strong>Rusak Berat</strong>.</li>
          <li>Pembaruan status dilakukan melalui menu <em>Daftar Aset &rarr; Edit Aset &rarr; Status Fisik &rarr; Rusak Berat</em>.</li>
          <li>Setelah status tersimpan sebagai Rusak Berat, tombol aksi <strong>[+ Ambil Komponen]</strong> dan <strong>[Lakukan Penghapusan]</strong> akan otomatis muncul pada halaman detail aset terkait.</li>
          <li>Pencatatan riwayat kanibalisasi mewajibkan penginputan nomor Laporan Kerusakan (LK) yang mendasari pengambilan komponen tersebut.</li>
        </ul>
      </div>
    </section>

    <hr class="border-slate-100">

    <!-- Section 3 -->
    <section>
      <h2 class="text-lg font-medium text-slate-900 mb-4">3. Peminjaman Aset Sementara</h2>
      <div class="prose prose-sm prose-slate max-w-none text-slate-600">
        <p>Mayoritas aset IPSRS adalah aset tetap (permanen) di dalam ruangan. Namun, sistem mengakomodasi peminjaman sementara untuk alat bantu kerja operasional.</p>
        <ul class="list-disc pl-4 mt-3 space-y-2">
          <li>Aset yang dapat dipinjam adalah aset yang sedang berstatus <strong>Tersedia</strong>.</li>
          <li>Proses peminjaman dilakukan langsung dari Halaman Detail Aset dengan mengeklik tombol <strong>[Pinjamkan]</strong>.</li>
          <li>Sistem akan mengubah status operasional aset menjadi <strong>Dipinjam</strong> dan mencatatnya pada log peminjaman.</li>
          <li>Setelah aset fisik dikembalikan, admin wajib mengeklik <strong>[Terima Pengembalian]</strong> pada detail aset untuk memulihkan statusnya kembali menjadi Tersedia.</li>
        </ul>
      </div>
    </section>

    <hr class="border-slate-100">

    <!-- Section 4 -->
    <section>
      <h2 class="text-lg font-medium text-slate-900 mb-4">4. End of Life (Penghapusan Aset)</h2>
      <div class="prose prose-sm prose-slate max-w-none text-slate-600">
        <p>Aset yang sudah tidak dapat diperbaiki atau nilai ekonomisnya habis harus melalui proses penghapusan (End of Life).</p>
        <ul class="list-disc pl-4 mt-3 space-y-2">
          <li>Penghapusan dalam sistem ini bersifat <em>soft-delete</em>; data aset akan diarsipkan dan tidak dihilangkan dari basis data untuk kebutuhan audit.</li>
          <li>Proses penghapusan <strong>mewajibkan</strong> lampiran Berita Acara (BA) Penghapusan yang sah.</li>
          <li>Aset yang telah dihapuskan akan dikunci dengan status <strong>Dihapuskan</strong> dan masuk ke dalam arsip logistik.</li>
        </ul>
      </div>
    </section>

  </div>
</div>
