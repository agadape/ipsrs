Modul Manajemen Suku Cadang (Gudang & Inventori)

Gambar 4.1. Halaman Daftar Suku Cadang dan Stok Gudang.

Modul ini adalah buku besar (buku stok) digital bagi teknisi IPSRS. Di sini Anda mendata seluruh onderdil, bahan bangunan, maupun *sparepart* (Suku Cadang) yang tersimpan di dalam gudang. Modul ini terhubung langsung secara ajaib dengan Modul Laporan Kerusakan, sehingga stok Anda akan selalu *update* (terbaru) tanpa perlu Anda hitung manual setiap hari.

1. Menambahkan Daftar Barang Baru ke Gudang
Langkah ini HANYA Anda lakukan jika IPSRS membeli **jenis onderdil baru** yang belum pernah terdaftar sama sekali di gudang (Misal: Rumah sakit baru pertama kali membeli "Freon R32").

Gambar 4.2. Jendela Formulir Tambah Barang Baru.

A. Membuka Formulir Barang Baru
Di sebelah kiri layar komputer Anda, cari dan klik menu bernama "Suku Cadang" (atau "Stok").
Perhatikan pojok kanan atas layar Anda, terdapat tombol berwarna biru/hijau bertuliskan "+ Tambah Barang". Klik tombol tersebut.
Sebuah jendela kecil (pop-up) akan muncul di tengah layar Anda.

B. Mengisi Informasi Barang
Nama Barang (Wajib Diisi): Ketikkan nama murni dari onderdil tersebut secara jelas (Contoh: "Freon R32" atau "Kabel Roll 50m").
Kategori (Wajib Diisi): Pilih dari kotak pilihan (dropdown) ke kelompok mana barang ini masuk (Contoh: Pilih "Kelistrikan", "Pipa & Saluran", atau "Suku Cadang AC").
Satuan (Wajib Diisi): Ketikkan satuan hitung barang tersebut agar stok tidak membingungkan (Contoh ketikan: "Tabung", "Meter", "Buah", atau "Liter").
Stok Minimum (Wajib Diisi Angka): Ini adalah fitur peringatan dini! Ketikkan angka batas bahaya. (Contoh: Anda mengetik angka "5"). Artinya, sistem akan langsung berteriak memberi peringatan merah jika sisa Freon di gudang tinggal 5 tabung.
Keterangan (Opsional): Ketikkan catatan tambahan jika perlu (Contoh: "Khusus untuk AC Daikin").

C. Menyimpan Barang
Klik tombol "Simpan". Barang tersebut sekarang sudah terdaftar di rak digital Anda, namun **stoknya masih 0 (Nol)**. Anda harus memasukkan jumlah barangnya melalui fitur Restok (Catat Masuk) di langkah selanjutnya.

2. Restok Barang (Mencatat Barang Masuk / Kulakan)
Langkah ini Anda lakukan setiap kali ada truk *supplier* datang mengantarkan onderdil baru ke gudang IPSRS.

Gambar 4.3. Jendela Formulir Catat Barang Masuk.

A. Membuka Formulir Barang Masuk
Masih di halaman "Suku Cadang", tepat di sebelah tombol "Tambah Barang" tadi, terdapat tombol bertuliskan "+ Catat Masuk (Restok)". Klik tombol tersebut.

B. Mengisi Bukti Barang Masuk
Nama Barang (Wajib Diisi): Klik kotak pencarian ini, ketik dan cari barang yang baru saja datang (Contoh: "Freon R32").
Jumlah (Wajib Diisi): Ketikkan berapa banyak barang yang datang (Contoh: Ketik "20").
Tanggal Masuk (Wajib Diisi): Pilih tanggal hari ini.
Nomor Dokumen/Faktur (Sangat Penting): Ketikkan nomor resi, nomor kuitansi, atau nomor Surat Jalan dari *supplier*. Ini wajib diisi sebagai bukti bahwa barang masuk secara sah (Audit).
Keterangan: Ketikkan asal usul barang (Contoh: "Pembelian dari Toko Jaya Abadi").

C. Memasukkan ke Gudang
Klik tombol "Simpan Transaksi". 
Sistem akan memuat sejenak. Jika tadinya stok Freon Anda adalah 0, maka secara otomatis akan berubah menjadi 20 Tabung!

3. Pengurangan Stok Otomatis (Barang Keluar)

Kehebatan Sistem: Anda TIDAK PERLU mencatat "Barang Keluar" secara manual setiap kali teknisi mengambil obeng atau freon untuk memperbaiki kerusakan di ruangan pasien!

A. Integrasi dengan Laporan Kerusakan
Sistem ini dirancang sangat cerdas. Pengurangan stok (*Barang Keluar*) terjadi secara otomatis di belakang layar saat Teknisi mengisi **Modul 2 (Laporan Kerusakan)**.
Ingatkah Anda saat teknisi memperbaiki AC bocor di Modul 2? Saat teknisi tersebut mengetikkan bahwa ia menghabiskan "1 Tabung Freon R32" di formulir penyelesaian perbaikan AC, sistem akan diam-diam berlari ke gudang digital ini.

B. Pemotongan Otomatis & Catatan Riwayat
Sistem akan **langsung memotong** stok Freon Anda dari 20 Tabung menjadi 19 Tabung!
Luar biasanya, jika Anda membuka tab/halaman "Riwayat Transaksi", Anda akan melihat sistem menuliskan catatan otomatis: "Freon R32 keluar sebanyak 1 Tabung. Digunakan untuk perbaikan Laporan Kerusakan nomor tiket PR202610-001".
Hal ini membuat pertanggungjawaban aset (Audit) menjadi sangat transparan, karena setiap barang yang keluar pasti jelas menempel pada tiket kerusakan ruangan mana.

4. Monitoring Peringatan Stok Menipis (Alert)

Gambar 4.4. Tampilan Dashboard dengan Peringatan Stok Kritis (Merah).

A. Peringatan Dini di Halaman Utama (Dashboard)
Ingat angka "Stok Minimum" yang Anda ketik di Langkah 1? Jika freon terus-terusan dipakai oleh teknisi hingga stoknya menyentuh angka 5 atau di bawahnya, sistem tidak akan diam saja.
Saat Kepala IPSRS atau teknisi membuka halaman awal (Dashboard) komputer mereka di pagi hari, layar akan memunculkan spanduk Peringatan Kritis berwarna Merah/Kuning menyala.

B. Tindakan Lanjut
Peringatan tersebut akan berbunyi: "Peringatan! Stok Freon R32 Menipis (Tersisa 5 Tabung)".
Tepat di sebelah tulisan itu, terdapat tombol pintasan "Restok Sekarang". Tombol ini akan memudahkan Kepala IPSRS untuk segera menelpon *supplier* agar memesan freon baru sebelum gudang benar-benar kehabisan stok, sehingga pelayanan perbaikan rumah sakit tidak pernah terhambat!
