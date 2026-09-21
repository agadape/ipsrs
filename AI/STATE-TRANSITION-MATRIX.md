# IPSRS State Transition Matrix

Tanggal: **15 September 2026**  
Status: **Approved target contract; implementasi saat ini masih memiliki writer status lama dan belum mematuhi seluruh transition ini**  
Dasar: `app/Config/IPSRS.php`, `Aset.php`, `LK.php`, `Preventif.php`, serta F05–F17 pada audit.

Evidence database 15 September 2026 memperkuat kebutuhan matrix ini: enum aktual `aset_series.status` masih terbatas pada lima nilai lama, sedangkan workflow LK aktual sudah menggunakan status tunggu vendor. Writer status baru tidak boleh diimplementasikan sebelum migration compatibility/value mapping ditetapkan.

## 1. Prinsip state machine

1. Status availability aset, kondisi aset, status LK, dan status jadwal adalah konsep terpisah. Satu kolom tidak boleh menyimpan arti campuran.
2. Hanya operation domain yang tervalidasi boleh mengubah status. View, form field, query parameter, atau generic update series tidak dapat memilih string status bebas.
3. Setiap transition memeriksa actor, existence, ownership/assignment, precondition, dan status awal di server sebelum write.
4. Transition dengan side effect multi-tabel dilakukan dalam transaksi. Bila satu write gagal, status utama dan history tidak boleh berubah sebagian.
5. Transition terminal tidak dapat dibuka kembali melalui action umum. Reopen/restoration hanya boleh ada sebagai action eksplisit yang tercatat dan belum masuk scope implementasi awal.
6. Semua timestamp transition memakai waktu server dan timezone aplikasi yang terdokumentasi. Nilai client hanya input pendukung yang tervalidasi.

## 2. Vocabulary canonical

### A. Kondisi fisik unit `aset_series.kondisi`

| Nilai | Arti |
| --- | --- |
| `Baik` | Unit berfungsi sesuai tujuan pemeriksaan terakhir. |
| `Kurang Baik` | Unit masih dapat dipakai terbatas atau membutuhkan perhatian, tanpa LK corrective aktif yang membuatnya unavailable. |
| `Rusak Ringan` | Ada kerusakan yang memerlukan corrective maintenance; availability mengikuti status LK/lifecycle. |
| `Rusak Berat` | Kerusakan berat; dapat menjadi kandidat kanibal/disposal setelah assessment. |

### B. Availability/lifecycle unit `aset_series.status`

| Nilai | Arti |
| --- | --- |
| `Tersedia` | Unit dapat dipakai dan tidak memiliki loan/LK aktif yang membuatnya unavailable. |
| `Dipinjam` | Ada tepat satu loan aktif. |
| `Dalam Perbaikan` | Ada LK corrective aktif yang sedang disurvei/diperbaiki. |
| `Menunggu Suku Cadang` | Ada LK active yang secara sah menunggu material gudang/kanibal. |
| `Menunggu Vendor` | Ada LK active yang dikirim/menunggu vendor. |
| `Dikanibal` | Donor tidak tersedia untuk operasi normal karena status/pengambilan komponen. |
| `Dihapuskan` | Terminal: unit sudah memiliki record penghapusan dan tidak boleh dipakai/diubah oleh workflow normal. |

`Rusak Berat`, `Aktif`, `Tidak Aktif`, `Di Gudang`, `Kanibal`, dan `Dibuang` tidak lagi menjadi nilai writer aktif untuk `aset_series.status`. Data lama dimigrasikan melalui mapping yang dicatat, bukan dipetakan diam-diam saat render.

### C. Status LK `laporan_kerusakan.status`

| Nilai | Arti |
| --- | --- |
| `Laporan Masuk` | LK berhasil dibuat dan belum diklaim/ditugaskan. |
| `Didisposisi` | Teknisi sudah ditetapkan; survei belum selesai. |
| `Survei` | Pemeriksaan awal sedang/baru dilakukan dan tindak lanjut belum diputuskan. |
| `Dalam Perbaikan` | Pekerjaan internal sedang dilakukan. |
| `Menunggu Suku Cadang` | Pekerjaan ditahan karena material belum tersedia/tercatat sah. |
| `Menunggu Vendor` | Pekerjaan ditahan karena proses vendor. |
| `Selesai` | Perbaikan/serah-terima memenuhi prasyarat dan waktu selesai dicatat. Terminal normal. |
| `Dibatalkan` | Administratively cancelled dengan alasan; bukan hard delete. Terminal normal. |

### D. Status jadwal preventive `jadwal_preventif.status`

| Nilai | Arti |
| --- | --- |
| `Belum` | Jadwal aktif dan belum dieksekusi. |
| `Dalam Pengerjaan` | Teknisi telah mulai mengerjakan LKP; optional bila UI membutuhkan draft/lock. |
| `Selesai` | LKP final tersimpan dan jadwal selesai. Terminal normal. |
| `Dijadwal Ulang` | Jadwal lama digantikan oleh jadwal baru yang mereferensikan alasan/reschedule source. Terminal normal. |
| `Dibatalkan` | Jadwal tidak dilaksanakan dengan alasan. Terminal normal. |

## 3. Transition LK

| Dari | Action | Actor | Guard | Ke | Side effect wajib |
| --- | --- | --- | --- | --- | --- |
| Tidak ada | Create portal/internal | Guest/Pelapor/Teknisi/Admin sesuai matrix akses | Payload valid; asset series bila dipilih valid dan non-terminal; rate limit untuk portal | `Laporan Masuk` | Insert LK, pelapor snapshot/ID bila login, created timestamp. Tidak mengubah aset sebelum LK insert berhasil. |
| `Laporan Masuk` | Claim | Teknisi/Admin | Teknisi aktif; conditional update memastikan belum assigned | `Didisposisi` | Set `id_pengguna_teknisi`, snapshot teknisi, assignment timestamp/history. Conflict jika sudah diklaim. |
| `Laporan Masuk` | Assign | Admin | Teknisi target aktif | `Didisposisi` | Sama seperti claim dengan actor admin dan reason bila reassign. |
| `Didisposisi` | Start survey | Assigned Teknisi/Admin | Assignment valid; LK non-terminal | `Survei` | Set tanggal/jam cek jika belum ada; series menjadi `Dalam Perbaikan` bila series terkait. |
| `Survei` | Start internal repair | Assigned Teknisi/Admin | Detail survei minimum terpenuhi sesuai form final | `Dalam Perbaikan` | Tindakan awal/history; series `Dalam Perbaikan`. |
| `Survei` atau `Dalam Perbaikan` | Wait for parts | Assigned Teknisi/Admin | Kebutuhan parts dicatat; tidak ada saldo gudang berubah tanpa detail/ledger sah | `Menunggu Suku Cadang` | Series `Menunggu Suku Cadang`; history status. |
| `Survei` atau `Dalam Perbaikan` | Wait for vendor | Assigned Teknisi/Admin | Detail vendor valid, tanggal kirim/estimasi sah | `Menunggu Vendor` | Series `Menunggu Vendor`; history status. |
| `Menunggu Suku Cadang` | Resume repair | Assigned Teknisi/Admin | Material tersedia dan, bila dipakai, detail/ledger berhasil | `Dalam Perbaikan` | History status; series `Dalam Perbaikan`. |
| `Menunggu Vendor` | Resume repair | Assigned Teknisi/Admin | Entry vendor memiliki return aktual atau kebijakan vendor final memperbolehkan lanjut | `Dalam Perbaikan` | History status; series `Dalam Perbaikan`. |
| `Survei`, `Dalam Perbaikan`, `Menunggu Suku Cadang`, atau `Menunggu Vendor` | Complete | Assigned Teknisi/Admin | Tindakan/hasil, timestamp valid, signature policy, vendor/parts policy, dan state asset valid | `Selesai` | Set tanggal/jam selesai sekali; kalkulasi RT/downtime; series `Tersedia` atau lifecycle hasil assessment yang sah; history status. |
| `Laporan Masuk`, `Didisposisi`, `Survei`, `Dalam Perbaikan`, `Menunggu Suku Cadang`, atau `Menunggu Vendor` | Cancel | Admin | Alasan wajib; reversal material/vendor/kanibal sesuai transaksi; tidak ada close | `Dibatalkan` | History cancellation; series kembali ke state yang sah setelah semua reversal berhasil. |

Tidak ada transition langsung dari `Laporan Masuk` ke `Selesai`. Tidak ada close ulang pada `Selesai`/`Dibatalkan`. Reopen bukan action pada contract ini; bila diperlukan nanti, harus membuat event baru yang menyimpan alasan, actor, timestamp, dan dampak angka laporan.

## 4. Transition lifecycle aset

| Dari | Action | Actor | Guard | Ke | Side effect wajib |
| --- | --- | --- | --- | --- | --- |
| `Tersedia` | Start LK survey/repair | Assigned Teknisi/Admin | LK valid sesuai tabel LK; series reference cocok | `Dalam Perbaikan` | LK history dan series update dalam transaksi bila operation memulai repair. |
| `Tersedia` | Create loan | Admin | Tidak ada loan aktif, tidak ada LK active, kondisi bukan `Rusak Berat` | `Dipinjam` | Insert loan aktif, actor, due date, history. |
| `Dipinjam` | Return | Admin | Tepat satu loan aktif series yang sama | `Tersedia` | Set return actual/status loan dan history dalam transaksi. |
| `Dalam Perbaikan` | LK waits parts | Assigned Teknisi/Admin | LK terkait valid | `Menunggu Suku Cadang` | Sinkron dengan status LK yang sama. |
| `Dalam Perbaikan` | LK waits vendor | Assigned Teknisi/Admin | LK terkait valid | `Menunggu Vendor` | Sinkron dengan status LK yang sama. |
| `Menunggu Suku Cadang` atau `Menunggu Vendor` | Resume LK | Assigned Teknisi/Admin | LK terkait valid | `Dalam Perbaikan` | Sinkron dengan status LK yang sama. |
| `Dalam Perbaikan`, `Menunggu Suku Cadang`, atau `Menunggu Vendor` | LK complete | Assigned Teknisi/Admin | LK close valid; assessment akhir tidak meminta lifecycle lain | `Tersedia` | Kondisi diperbarui bila ada assessment sah; history LK tetap menjadi sumber event. |
| `Tersedia` atau `Dalam Perbaikan` | Mark donor for kanibal | Admin | Reason/LK recipient, component availability, recipient valid, no loan | `Dikanibal` | Insert history kanibal/component transfer; donor condition/status dan recipient component history konsisten. |
| `Tersedia` atau `Dikanibal` | Disposal | Admin | Tidak ada loan/LK active; BA dan reason valid; policy evidence terpenuhi | `Dihapuskan` | Insert disposal/BA metadata, timestamp, actor, history. |

`Dihapuskan` tidak memiliki transition keluar pada scope sekarang. `Dikanibal` hanya kembali tersedia melalui workflow restoration/recommission yang belum dibuat; tidak boleh berubah karena edit/mutasi/return/portal/close LK biasa.

## 5. Transition lokasi aset

| Dari | Action | Actor | Guard | Ke | Side effect wajib |
| --- | --- | --- | --- | --- | --- |
| Lokasi aktif A | Mutasi lokasi | Admin | Series non-terminal; lokasi tujuan valid; alasan wajib | Lokasi aktif B | Update `aset_series.id_lokasi` dan insert `riwayat_lokasi_aset` dengan series ID, asal, tujuan, actor, waktu dalam satu transaksi. |

Lokasi tidak diubah oleh generic edit series, portal report, scan/ping, loan, return, atau close LK. Data GPS hanya boleh tercatat sebagai telemetry dan tidak mengganti lokasi aktif.

## 6. Transition loan dan disposal record

| Record | Dari | Action | Ke | Guard |
| --- | --- | --- | --- | --- |
| `peminjaman_aset` | Tidak ada | Create | `Dipinjam` | Series `Tersedia`; satu loan aktif; peminjam/date valid. |
| `peminjaman_aset` | `Dipinjam` | Return | `Selesai` | Series masih `Dipinjam`; return date server/validated; actor Admin. |
| `penghapusan_aset` | Tidak ada | Create disposal | Final record | Series eligible; no active loan/LK; BA policy; actor Admin. |

Peminjaman aktif tidak boleh dihapus. Pembetulan administrasi memakai entry koreksi/audit trail, bukan menghapus record histori.

## 7. Transition preventive maintenance

| Dari | Action | Actor | Guard | Ke | Side effect wajib |
| --- | --- | --- | --- | --- | --- |
| Tidak ada | Create schedule | Admin | Series, category, date/time, assigned active technician valid | `Belum` | Insert schedule dengan ID series/technician. |
| `Belum` | Start LKP | Assigned Teknisi/Admin | Tanggal/action menurut policy; LKP belum final | `Dalam Pengerjaan` atau tetap `Belum` bila draft tidak digunakan | Create/open one LKP draft idempotently. |
| `Belum` atau `Dalam Pengerjaan` | Submit LKP result `Siap Pakai` | Assigned Teknisi/Admin | Semua checklist required tersimpan; no duplicate final | `Selesai` | Finalize LKP, set timestamp/actor, series history link. |
| `Belum` atau `Dalam Pengerjaan` | Submit LKP result `Perlu Perbaikan` | Assigned Teknisi/Admin | Semua checklist required tersimpan; no duplicate final | `Selesai` | Finalize LKP; create one linked LK in same transaction or durable pending event with retry key. |
| `Belum` atau `Dalam Pengerjaan` | Reschedule | Admin | Alasan wajib | `Dijadwal Ulang` | Create replacement schedule and link source/replacement. |
| `Belum` atau `Dalam Pengerjaan` | Cancel | Admin | Alasan wajib | `Dibatalkan` | History actor/reason. |

`Selesai`, `Dijadwal Ulang`, dan `Dibatalkan` tidak dapat menerima LKP baru. Bila inspection harus diulang, buat jadwal baru yang menautkan predecessor bila relevan.

## 8. Inventory and kanibal operation contract

Stok bukan state string sederhana. Kontraknya:

1. Debit Gudang hanya dilakukan bersama insert ledger dan detail parts LK dalam satu transaksi.
2. Saldo tidak boleh negatif. Check saldo dan decrement harus atomik pada database.
3. Setiap debit/reversal punya `operation_key` atau reference immutable untuk mencegah duplicate submit.
4. Reversal Gudang hanya mereverse ledger Gudang yang masih aktif dan milik detail/LK asal.
5. Kanibal memindahkan komponen donor ke penerima/history; operation itu tidak menambah saldo `barang_persediaan` kecuali proses penerimaan gudang terpisah memang terjadi.
6. Delete LK dengan side effect tidak dipakai sebagai shortcut. Cancellation mengikuti transition LK dan reversal source-specific.

## 9. Perhitungan waktu dan laporan

| Nilai | Start | End | Aturan |
| --- | --- | --- | --- |
| Response time | `tanggal` + `jam_laporan` | `tanggal_cek` + `jam_cek` | Selisih menit server-side; nilai 0 valid; end sebelum start ditolak atau ditandai invalid. |
| Down time | `tanggal` + `jam_laporan` atau timestamp downtime yang disepakati | `tanggal_selesai` + `jam_selesai` | Definisi harus sama di UI, controller, export, dan skripsi. Nilai null berarti tidak tersedia, bukan otomatis nol. |
| SLA | Response time dibanding `IPSRS::SLA_RESPONSE_TIME` | N/A | Angka SLA adalah konfigurasi aplikasi sampai SPO sumber dapat dibuktikan. |

Angka selesai/timestamp tidak boleh ditulis ulang oleh update detail setelah LK `Selesai`. Koreksi angka memerlukan operation audit terpisah yang belum ada dalam scope awal.

## 10. Implementation mapping

| Current writer/problem | Contract target |
| --- | --- |
| `app/Config/IPSRS.php` memetakan LK ke `Aktif`, `Tidak Aktif`, dan status di luar `STATUS_ASET` | Ganti dengan vocabulary canonical dan satu transition service/operation. |
| `Aset.php` menulis `Di Gudang`, `Kanibal`, `Dibuang`, `Aktif`, `Dipinjam`, `Tersedia`, `Rusak Berat`, `Dihapuskan` dari beberapa action | Semua action diubah menggunakan lifecycle transition yang tervalidasi. |
| `LK.php` menerima `status_baru` dari request dan dapat sync asset tanpa full guard | `status_baru` hanya action transition terdaftar; actor/state/side-effect dites. |
| `Portal.php` mengubah aset sebelum persistence LK | Insert LK dan lifecycle update mengikuti satu transaction/policy. |
| `Preventif.php` hanya memakai `Belum`/`Selesai` dan auto-LK belum idempotent | Tambah state/guard sesuai matriks dan relation source LKP–LK. |
| Generic edit series dapat mengubah lokasi | Lokasi hanya lewat mutation operation. |

## 11. Evidence penutupan

F07, F09, F10, F12, F13, F17, serta bagian lifecycle F11 hanya dapat ditutup jika:

- semua writer lama diganti atau dibatasi sehingga tidak dapat melewati transition;
- test menguji setiap transition valid dan minimal satu transition invalid per state;
- test membuktikan state terminal tidak direactivate lewat endpoint lain;
- failure pada history/ledger/detail menggagalkan perubahan status utama;
- export/detail series memperlihatkan status/lokasi dari record authoritative;
- dokumentasi user dan skripsi memakai vocabulary yang sama.
