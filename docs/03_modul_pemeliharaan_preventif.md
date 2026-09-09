Modul Pemeliharaan Preventif (Preventive Maintenance)

Gambar 3.1. Halaman Dashboard Pemeliharaan Preventif (Jadwal PM).

Modul ini adalah buku agenda cerdas bagi teknisi IPSRS. Daripada menunggu alat rusak dan dikeluhkan oleh staf ruangan (perbaikan Kuratif), IPSRS wajib melakukan perawatan rutin terjadwal (Preventif) seperti membersihkan saringan AC atau mengecek voltase panel listrik, agar sarana rumah sakit berumur panjang.

1. Pembuatan Jadwal Preventif
Langkah ini biasanya dilakukan oleh Koordinator IPSRS untuk membagikan tugas pemeliharaan rutin kepada teknisi-teknisinya di awal bulan.

Gambar 3.2. Halaman Formulir Pembuatan Jadwal Baru.

A. Membuka Formulir Jadwal Baru
Di menu navigasi kiri layar Anda, klik menu bernama "Pemeliharaan Preventif".
Di sudut kanan atas halaman tersebut, terdapat tombol biru bertuliskan "+ Tambah Jadwal". Klik tombol itu, lalu sebuah jendela pop-up akan muncul.

B. Mengisi Detail Tugas Pemeliharaan
Aset (Wajib Diisi): Klik kotak pencarian ini lalu ketikkan nama alat yang harus dirawat (Contoh: "AC Split Ruang ICU" atau "Genset Utama"). Pastikan Anda memilih aset dari daftar yang muncul.
Teknisi Penanggung Jawab (Wajib Diisi): Klik kotak pilihan (dropdown) ini dan pilih nama teknisi yang diberi tugas (Contoh: "Budi Santoso"). Hanya teknisi yang namanya dipilih yang akan bertanggung jawab atas alat ini.
Tanggal & Jam (Wajib Diisi): Tentukan kapan tugas ini harus dilaksanakan. (Contoh: Tanggal 15 Oktober 2026, Jam 09:00). 
⚠️ Peringatan Keras: Sistem sangat disiplin. Anda dilarang memasukkan tanggal dan jam yang sudah berlalu/kedaluwarsa. Sistem otomatis akan menolak jadwal yang dibuat di masa lalu (backdate).

C. Menyimpan Jadwal
Klik tombol "Simpan" di bagian bawah.
Jadwal tugas tersebut sekarang akan berjejer rapi di dalam tabel halaman utama, lengkap dengan status abu-abu bertuliskan "Belum" dikerjakan.

2. Eksekusi Lembar Kerja Preventif (LKP)
Saat hari-H tiba, teknisi (misalnya Budi) harus pergi ke lokasi alat, melakukan perawatan (seperti membersihkan debu atau mengukur tegangan listrik), lalu melaporkan hasilnya langsung lewat sistem. 

Gambar 3.3. Halaman Pengisian Formulir LKP (Lembar Kerja Preventif).

A. Membuka Lembar LKP
Cari jadwal tugas Anda di tabel Pemeliharaan Preventif. Jika Anda terlambat mengerjakannya dari tanggal yang ditentukan, sistem akan memberikan teguran dengan mengubah status baris tersebut menjadi Merah menyala ("Terlambat").
Di ujung kanan baris tugas Anda, klik tombol hijau bergambar kertas bertuliskan "Isi LKP". 
Anda akan masuk ke dalam halaman formulir raksasa semacam "Checklist" (Daftar Periksa).

B. Mengisi Kategori & Kehebatan Sistem (Auto-Template)
Kategori Alat (Wajib Diisi): Ketikkan jenis alat tersebut (Contoh: "Pendingin Ruangan / AC").
Kehebatan Sistem (Mesin Pembelajar): Saat Anda mengetikkan Kategori, keajaiban akan terjadi. Sistem akan langsung memunculkan tabel berisi deretan komponen apa saja yang wajib Anda periksa secara spesifik untuk kategori AC (misal: "Cuci Filter", "Cek Tekanan Freon", "Cek Pipa Drainase"). 
Bagaimana jika komponennya belum ada? Cukup ketik saja nama komponen baru (Misal: "Cek Kelistrikan AC") di baris kosong yang disediakan. Begitu Anda menekan tombol simpan nanti, sistem otomatis "mempelajari" dan merekam komponen baru Anda secara permanen. Sehingga bulan depan, jika ada teknisi lain merawat AC, tulisan "Cek Kelistrikan AC" tersebut sudah langsung muncul di tabel *checklist* miliknya!

C. Mengisi Hasil Inspeksi Per Komponen
Anda harus menilai setiap komponen di tabel:
- Jika jenisnya Inspeksi (Pengamatan mata): Pilih "Baik" atau "Rusak".
- Jika jenisnya Pengukuran: Ketikkan angka alat ukur Anda (Contoh: ketik "220" pada komponen Voltase, lalu isi kolom Satuan dengan "Volt").

D. Menentukan Hasil Akhir (Sangat Krusial)
Di bagian atas formulir, Anda diwajibkan menyimpulkan kondisi keseluruhan alat tersebut di kotak dropdown "Hasil Pemeriksaan Akhir". Anda berhadapan dengan dua pilihan besar:
- Pilihan 1: "Laik Pakai / Normal" (Pilih ini jika perawatan berjalan lancar, alat dicuci bersih, dan berfungsi baik).
- Pilihan 2: "Perlu Perbaikan" (Pilih ini jika saat Anda merawat AC, Anda menemukan kerusakan parah seperti kompresor mati yang tidak bisa Anda selesaikan saat itu juga).

3. Tindak Lanjut Otomatis (Auto-Generate Tiket Kuratif)
Langkah ini menjelaskan apa yang terjadi saat Anda menekan tombol "Simpan LKP" di paling bawah formulir.

Skenario A (Jika Anda memilih "Laik Pakai"):
Sistem akan menyimpan laporan *checklist* Anda, mengubah status jadwal tugas Anda menjadi Hijau ("Selesai"), dan mengembalikan Anda ke halaman utama dengan ucapan sukses. Tugas Anda beres!

Skenario B (Jika Anda memilih "Perlu Perbaikan"):
Kehebatan Sistem: Karena alat tersebut ternyata rusak dan butuh turun mesin atau butuh penggantian *sparepart*, sistem TIDAK akan membiarkan masalah ini menggantung.
Begitu Anda mengeklik Simpan, sistem akan melakukan *Auto-Generate* (membuat otomatis) sebuah Tiket Laporan Kerusakan (LK) baru!
Anda akan langsung "dilempar" (diarahkan) masuk ke dalam halaman Tiket Perbaikan Kuratif. Sistem akan membuatkan Nomor Tiket baru, menyalin nama AC tersebut, dan menyalin catatan Anda menjadi "Temuan Preventif".
Di halaman perbaikan inilah, Anda baru diizinkan oleh sistem untuk meminta Suku Cadang dari gudang atau meminta bantuan Vendor Pihak Ketiga (Sesuai dengan cara kerja pada Panduan Modul 2).
