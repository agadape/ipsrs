# Technical UAT — Preventive Maintenance

Tujuan UAT ini adalah membuktikan satu workflow preventive berjalan dari jadwal sampai tindak lanjut corrective, tanpa input database manual.

## Data yang disiapkan

- Akun **Teknisi** yang aktif.
- Satu aset series/unit fisik yang aktif.
- Satu jadwal preventive dengan teknisi tersebut, status **Belum**.
- Jadwal sebaiknya memakai aset dan lokasi agar hasilnya mudah diverifikasi.

## Alur utama yang harus dilakukan

1. Login sebagai **Teknisi**.
2. Buka menu **Pemeliharaan Preventif**.
3. Pilih jadwal berstatus **Belum**, lalu buka/isi **LKP**.
4. Isi kategori alat, pilih **Perlu Perbaikan**, isi lokasi sesuai, nama perwakilan unit, dan catatan temuan yang jelas.
5. Isi minimal empat checklist berikut:

   | Tipe | Contoh komponen | Contoh hasil |
   | --- | --- | --- |
   | Inspeksi | Kabel daya | Ya / Tidak |
   | Service | Pembersihan filter | Ya / Tidak |
   | Pengukuran | Tegangan | `220` + satuan `V` |
   | Teks | Kondisi panel | `Ada retak pada penutup panel` |

6. Klik **Simpan LKP** sekali saja dan tunggu respons aplikasi.
7. Sistem seharusnya membuka detail **LK kuratif** baru, dengan keluhan yang memuat nomor LKP asal (`Temuan PM [LKP-...]`).
8. Kembali ke menu Preventif dan pastikan jadwal yang sama berstatus **Selesai**.
9. Buka hasil LKP dan pastikan nama perwakilan unit, empat checklist, nilai `220 V`, jawaban Teks, catatan, dan hasil **Perlu Perbaikan** tampil kembali.
10. Coba klik simpan/submit ulang menggunakan halaman sebelumnya atau refresh POST bila aman dilakukan. Sistem tidak boleh membuat LKP atau LK kuratif kedua.

## Hasil yang harus dicatat

- Nomor jadwal, nomor LKP, dan nomor LK kuratif yang terbentuk.
- Screenshot halaman hasil LKP.
- Screenshot detail LK kuratif yang memuat asal temuan PM.
- Screenshot daftar jadwal yang menunjukkan status **Selesai**.
- Hasil percobaan submit ulang: harus ditolak/diarahkan ke hasil sebelumnya tanpa data duplikat.

## Kriteria lulus

- Satu jadwal menghasilkan tepat satu LKP.
- Semua tipe checklist tersimpan dan terbaca ulang tanpa nilai hilang.
- Jadwal berubah menjadi **Selesai** hanya setelah LKP valid tersimpan.
- Hasil **Perlu Perbaikan** membuat tepat satu LK kuratif.
- LK kuratif menyimpan teks lineage nomor LKP asal.
- Submit ulang tidak menghasilkan LKP/LK duplikat.

## Jika gagal

Catat langkah ke berapa, pesan error, nomor data yang terlanjur terbentuk, serta screenshot. Jangan menghapus data langsung dari database; kirimkan bukti tersebut agar alur yang gagal dapat ditelusuri dari UI, request, dan data hasilnya.

## Batas evidence UAT ini

UAT ini membuktikan workflow aplikasi yang terlihat pengguna. Ia belum membuktikan konkurensi MySQL dua teknisi pada waktu sama, relasi FK LKP→LK (lineage saat ini masih teks), atau tanda tangan digital LKP.
