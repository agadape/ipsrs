# UAT Teknis — Kanibalisasi Komponen

Gunakan **data uji/non-produktif**. Satu submit yang sukses mengubah riwayat, detail LK, komponen donor, dan komponen penerima; jangan memakai aset nyata bila hasilnya tidak boleh tersimpan.

## Persiapan

- Login sebagai **Admin**.
- Siapkan aset donor berstatus **Rusak Berat** dengan satu komponen berstatus selain `Tidak Ada`.
- Siapkan aset penerima berstatus `Dalam Perbaikan` atau status aktif lain, dan LK yang terhubung ke aset penerima dengan status `Survei`, `Dalam Perbaikan`, `Menunggu Suku Cadang`, atau `Menunggu Vendor`.
- Catat nama komponen donor dan jumlah detail suku cadang LK sebelum mulai.

## Alur utama

1. Login Admin → buka LK penerima → bagian **Suku Cadang** → pilih **Kanibal dari Aset Lain**.
2. Pilih donor `Rusak Berat` → pastikan dropdown komponen terisi dan komponen `Tidak Ada` tidak ditawarkan.
3. Pilih satu komponen, pilih kondisinya, isi catatan bila perlu → klik **Catat Kanibal**.
4. Buka ulang LK: pastikan muncul satu detail baru dengan sumber `Kanibal`, nama komponen tepat, dan jumlah `1`.
5. Buka aset donor: komponen tersebut harus menjadi `Tidak Ada`.
6. Buka aset penerima: komponen harus tercatat/terbarui sebagai `Hasil Kanibal` dan riwayat kanibal dapat ditelusuri.
7. Buka menu **Kanibal**: pastikan nomor LK, donor, penerima, komponen, petugas, serta approval Admin tercatat.

## Negative checks

- Ulang submit komponen donor yang sama: harus gagal; tidak boleh ada detail LK atau history tambahan.
- Coba URL/form sebagai Teknisi atau Pelapor: modul/POST harus ditolak.
- Ubah hidden `id_aset_penerima` melalui browser ke aset yang bukan milik LK: harus gagal tanpa perubahan data.
- Coba memilih donor selain `Rusak Berat` atau penerima `Rusak Berat`/`Dihapuskan`: server harus menolak tanpa perubahan data.
- Coba menambahkan kanibal pada LK `Selesai`: harus ditolak.

## Bukti yang disimpan

- Screenshot sebelum dan sesudah dari LK, komponen donor, komponen penerima, dan riwayat kanibal.
- Catat akun Admin, nomor LK, tanggal/jam, donor, penerima, dan nama komponen.
- Bila ada hasil gagal, catat pesan error dan buktikan detail/history tidak bertambah.

## Batas UAT

UAT ini membuktikan alur aplikasi yang dijalankan manusia. Ia belum membuktikan locking MySQL dua koneksi atau approval formal berbasis user-ID/tanda tangan digital.
