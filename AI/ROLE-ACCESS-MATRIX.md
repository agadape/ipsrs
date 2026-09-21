# IPSRS Role & Object Access Matrix

Tanggal review terakhir: **18 September 2026**  
Status: **Partially enforced and request-tested; actor-ID migration dan full browser matrix belum selesai**  
Dasar: `app/Config/Routes.php`, controller aktif, dan finding F02/F09/F15/F16/F23 dalam `AI/PROJECT-AUDIT-REPORT.md`.

Evidence database 15 September 2026 menegaskan bahwa `pengguna.role` masih varchar bebas dan role historis memiliki perbedaan kapitalisasi, sedangkan LK/LKP/jadwal masih menyimpan pelapor/teknisi sebagai nama display tanpa FK user. Contract ini tetap berlaku sebagai target Iteration 2; normalisasi role dan actor ID memerlukan migration yang kompatibel dengan schema dump aktual.

## 1. Tujuan

Dokumen ini menjadi kontrak backend untuk autentikasi, role-based access control, object-level authorization, dan state authorization. Menu yang disembunyikan atau tombol yang disabled tidak memenuhi kontrak ini. Setiap controller action yang mengubah atau mengungkap data internal wajib menjalankan rule yang sama pada server.

## 2. Role canonical

| Role tersimpan | Session canonical | Tujuan | Catatan implementasi |
| --- | --- | --- | --- |
| `Admin` | `admin` | Mengelola master, aset, user, stok, lifecycle, jadwal, serta mengawasi seluruh pekerjaan | Role operasional IPSRS saat ini dipetakan ke Admin; role baru tidak dibuat dalam Iteration 0. |
| `Teknisi` | `teknisi` | Menangani pekerjaan yang diklaim/ditugaskan, melaksanakan preventive maintenance, dan melihat data operasional yang diperlukan | Tidak mengelola user, master, stok manual, disposal, atau mutasi lokasi. |
| `Pelapor` | `pelapor` | Membuat laporan dan melihat laporan miliknya | Tidak mengakses master, inventory, daftar seluruh LK, atau data pengguna lain. |
| Tidak login | `guest` | Menggunakan portal pelaporan dan scan QR yang memang dibuka publik | Tidak dapat membaca data internal atau melakukan mutation internal. |

Nilai role dibaca dengan normalisasi lowercase saat login. Database dan UI harus menyimpan/menggunakan satu bentuk display canonical: `Admin`, `Teknisi`, `Pelapor`. Nilai lain ditolak ketika user dibuat atau diubah.

Tidak ada role `Kepala`, `Approver`, atau `Superadmin` dalam scope saat ini. Kolom/nama `disetujui_oleh` tidak memberi permission dan tidak boleh disebut approval formal sebelum workflow baru dibuat.

## 3. Identitas dan ownership yang wajib tersedia

| Konsep | Identifier authoritative | Aturan |
| --- | --- | --- |
| User login | `pengguna.id` | Session menyimpan `user_id`, role canonical, nama display, dan unit. Nama bukan identifier otorisasi. |
| Unit fisik aset | `aset_series.id` | Semua lifecycle, LK aset-terpilih, lokasi, loan, disposal, kanibal, dan PM unit harus memakai ID series. |
| Pelapor LK internal | `laporan_kerusakan.id_pengguna_pelapor` | Diisi dari session saat Pelapor mengirim laporan. Kolom nama/unit hanya snapshot tampilan. |
| Teknisi LK | `laporan_kerusakan.id_pengguna_teknisi` | Diisi saat claim/assignment. Kolom `teknisi` hanya snapshot tampilan. |
| Jadwal teknisi | `jadwal_preventif.id_pengguna_teknisi` | Menentukan siapa dapat membuka dan menyimpan LKP. |
| Actor mutation | `id_pengguna_actor` atau audit field equivalent | Setiap operasi penting menyimpan actor ID, bukan hanya nama. |
| Detail LK | `detail_suku_cadang_lk.id_lk` dan `detail_vendor_lk.id_lk` | Akses detail selalu diturunkan dari akses terhadap LK induknya. |

UUID atau nomor order tidak membuktikan hak akses. Identifier hanya menemukan objek, lalu policy memeriksa actor, ownership, assignment, dan state.

## 4. Rule global untuk endpoint

1. Semua mutation menggunakan POST, CSRF valid, session valid bila endpoint internal, server validation, dan authorization object-level sebelum write.
2. Tamu hanya dapat mengirim `POST /lapor` dan `POST /ipsrs/aset/{uuid}/ping`. Keduanya mempunyai validasi payload/rate-limit sesuai Iteration 1 dan tidak memberi akses data internal.
3. Pelapor hanya dapat membaca LK dengan `id_pengguna_pelapor = session.user_id`. Bila LK portal anonim memang perlu dilihat kembali, gunakan token akses satu kali yang tidak dapat dipakai untuk mutation; token belum menjadi scope implementasi ini.
4. Teknisi dapat membaca LK yang belum diklaim untuk tujuan klaim dan LK yang `id_pengguna_teknisi = session.user_id`. Akses LK milik teknisi lain tidak diberikan, kecuali Admin menugaskan ulang melalui action khusus yang tercatat.
5. Admin dapat membaca seluruh record internal serta melakukan action yang dicantumkan pada matrix.
6. Akses objek yang tidak ditemukan atau bukan milik actor menghasilkan respons yang tidak membocorkan data. UI HTML menggunakan redirect/flash yang aman; JSON menggunakan 404 untuk objek tidak terlihat atau 403 untuk scope yang diketahui tetapi dilarang, dipilih konsisten per endpoint.
7. Session user nonaktif tidak dapat dipakai untuk action baru. Setelah Iteration 2, filter/action kritis harus memverifikasi `pengguna.aktif` atau mekanisme revocation setara.
8. Error database, exception, dan validation tidak memuat password, token, path storage internal, stack trace, atau data user lain.

## 5. Matriks route publik dan autentikasi

| Route | Method | Guest | Pelapor | Teknisi | Admin | Policy / kondisi |
| --- | --- | --- | --- | --- | --- | --- |
| `/` | GET | Allow | Allow | Allow | Allow | Redirect saja. |
| `/lapor` | GET | Allow | Allow | Allow | Allow | Form laporan publik; tidak menampilkan data internal. |
| `/lapor` | POST | Allow | Allow | Allow | Allow | Create LK portal dengan rate limit, validation, dan asset series hanya jika UUID valid. Tidak mengubah aset sebelum LK berhasil dibuat. |
| `/lapor/sukses` | GET | Allow | Allow | Allow | Allow | Halaman sukses tanpa detail LK sensitif. |
| `/login` | GET/POST | Allow | Allow | Allow | Allow | Login rate-limited; POST hanya credential; session regenerate saat sukses. |
| `/register` | GET/POST | Deny/404 | Deny/404 | Deny/404 | Deny/404 | Registrasi publik sengaja tidak masuk scope; route/view dihapus atau 404 konsisten. |
| `/logout` | POST | Deny | Allow self | Allow self | Allow self | Logout tidak lagi GET mutation; CSRF valid. |
| `/ipsrs/aset/scan/{seriesUuid}` | GET | Allow | Allow | Allow | Allow | Tampilkan data minimum unit yang diperlukan scan; jangan tampilkan master/internal history. |
| `/ipsrs/aset/{seriesUuid}/ping` | POST | Allow | Allow | Allow | Allow | Telemetry perangkat, bukan permission atau perubahan lokasi master; rate limited dan UUID series valid. |

## 6. Matriks dashboard, aset, dan lifecycle

| Resource / route group | Pelapor | Teknisi | Admin | Object and state rule |
| --- | --- | --- | --- | --- |
| `/ipsrs` dashboard | Own-only dashboard | Operational dashboard terbatas | Semua KPI | Pelapor hanya LK miliknya; dashboard tidak mengirim payload data global ke browser pelapor. |
| `GET /ipsrs/aset` dan detail series | Deny kecuali lookup laporan yang diproteksi | Allow read | Allow read | Data authoritative adalah series. Pelapor tidak mendapat inventory browser penuh hanya untuk membuat laporan. |
| Tambah/edit katalog dan series | Deny | Deny | Allow | Admin only; edit tidak dapat mengganti lokasi aktif tanpa operasi mutasi. |
| QR series | Deny | Allow | Allow | QR merepresentasikan series UUID dan tidak mengandung secret/session data. |
| Mutasi lokasi | Deny | Deny | Allow | Hanya unit series non-terminal; update lokasi aktif dan history dalam satu transaksi. |
| Pinjam aset | Deny | Deny | Allow | Hanya state `Tersedia`, tanpa pinjaman aktif; POST + CSRF. |
| Kembali aset | Deny | Deny | Allow | Hanya loan aktif pada series yang sama; POST + CSRF; menjadi `Tersedia` atau state hasil inspeksi yang terdokumentasi. |
| Penghapusan dan BA | Deny | Deny | Allow | Hanya state yang eligible, tidak ada LK aktif/loan aktif; BA mengikuti policy upload. Disposal terminal. |
| Download BA | Deny kecuali policy khusus | Deny | Allow | Admin dapat melihat BA; pihak lain hanya bila requirement resmi menambah policy. |

## 7. Matriks corrective maintenance (LK)

| Action | Pelapor | Teknisi | Admin | Guard wajib |
| --- | --- | --- | --- | --- |
| List LK | Hanya milik sendiri | Unassigned + assigned-to-self | Semua | Query difilter server-side menggunakan user ID/status scope. |
| Lihat LK | Hanya milik sendiri | Unassigned untuk claim preview atau assigned-to-self | Semua | Detail parts/vendor/signature mengikuti akses LK induk. |
| Buat LK internal | Allow, menjadi owner | Allow untuk laporan operasional | Allow | Series optional hanya jika requirement menerima laporan manual; pilihan series harus dipersist. |
| Delete/cancel LK | Deny | Deny | Allow action cancellation | Hard delete tidak digunakan untuk LK dengan side effect. Cancellation harus state transition dan reversal aman. |
| Claim LK | Deny | Allow | Allow / assign | Hanya `Laporan Masuk`; atomic conditional update; teknisi kedua menerima conflict. |
| Assign/reassign teknisi | Deny | Deny | Allow | State non-terminal; actor/assignment history tersimpan. |
| Simpan survei/detail | Deny | Assigned-to-self | Allow | Hanya `Didisposisi` atau `Survei` sesuai state matrix. |
| Ubah status/perbaikan | Deny | Assigned-to-self | Allow | Semua transition diperiksa server-side. |
| Tambah/hapus parts | Deny | Assigned-to-self | Allow | State yang menerima parts; inventory transaction berhasil atau seluruh action gagal. |
| Tambah/update vendor | Deny | Assigned-to-self | Allow | State yang menerima vendor; tanggal logis dan return policy berlaku. |
| TTD pelapor | Owner jika policy bukti serah-terima dipilih | Deny | Allow witness bila diperlukan | Hanya LK state siap serah-terima; actor/timestamp/version tersimpan. |
| Close LK | Deny | Assigned-to-self | Allow | Prasyarat detail, parts/vendor, signature, dan state sesuai matrix; close idempotent/terminal. |

## 8. Matriks preventive maintenance

| Action | Pelapor | Teknisi | Admin | Guard wajib |
| --- | --- | --- | --- | --- |
| List jadwal | Deny | Jadwal milik sendiri | Semua | Filter server-side memakai `id_pengguna_teknisi`. |
| Tambah jadwal | Deny | Deny | Allow | Series, tanggal, teknisi, kategori, dan status awal tervalidasi. |
| Reschedule/cancel | Deny | Deny | Allow | Alasan dan history wajib; tidak berlaku pada jadwal terminal kecuali workflow reopening resmi. |
| Buka LKP | Deny | Jadwal milik sendiri dan belum terminal | Allow | Series/jadwal valid; template yang dipakai tersnapshot. |
| Simpan LKP | Deny | Jadwal milik sendiri | Allow | Idempotency guard, semua tipe jawaban tersimpan, hasil valid. |
| Lihat hasil LKP | Deny | Jadwal/LKP milik sendiri | Semua | History series dapat menautkan LKP yang sah. |
| Buat auto-LK temuan | Tidak langsung | Dipicu oleh simpan LKP yang sah | Dipicu oleh simpan LKP sah | Bukan route bebas; relation `source_lkp_id` dan transaction/idempotency wajib. |

## 9. Matriks stok, vendor, kanibal, dan master

| Resource | Pelapor | Teknisi | Admin | Guard wajib |
| --- | --- | --- | --- | --- |
| Daftar/riwayat stok | Deny | Read untuk memilih parts pada LK assigned | Allow | Teknisi hanya menerima projection item/availability yang diperlukan; tidak dapat stock adjustment manual. |
| Tambah barang dan stok masuk/keluar manual | Deny | Deny | Allow | Semua adjustment memiliki reason, reference, actor, dan ledger immutable. |
| Vendor master | Deny | Read untuk LK assigned | Allow CRUD | Detail vendor LK tetap mengikuti ownership LK. |
| Kanibal history | Deny | Read hanya pada LK assigned bila diperlukan | Allow all | Jangan kirim seluruh data donor ke actor yang tidak berhak. |
| Buat kanibal | Deny | Deny | Allow | Admin melakukan/menyetujui operation dalam scope saat ini; donor/penerima/LK/status/komponen harus valid. |
| Kategori aset/kode kerusakan | Deny | Read untuk form yang sah | Allow CRUD | Delete ditolak jika masih direferensikan atau dilindungi migration policy. |
| Pengguna | Deny | Deny | Allow CRUD | Admin tidak dapat menghapus/mematikan akun sendiri tanpa guard khusus; password tidak pernah dikembalikan dalam response. |

## 10. Matriks pelaporan dan dokumen

| Resource | Pelapor | Teknisi | Admin | Guard wajib |
| --- | --- | --- | --- | --- |
| Dashboard/report summary | Own-only bila memang dibutuhkan | Operational scope terbatas | Semua | Dataset/filter dibangun di SQL sesuai role; bukan filter browser. |
| Excel/print LK/PM | Deny | Deny | Allow | Period, unit, identifier series, dan snapshot mengikuti contract laporan; export text bound as string. |
| Statistik KPI/SLA | Own-only atau deny | Scope operasi sendiri bila diperlukan | Semua | Definisi RT/downtime dan timezone documented; angka tidak dihitung dari data tak berhak. |

## 11. Perubahan yang harus dilakukan pada Iteration 2

1. Normalisasi role saat login dan validasi user create/edit.
2. Tambahkan guard reusable untuk `requireRole`, `requireLkAccess`, `requireSeriesAccess`, `requireAssignedTechnician`, dan `requireAllowedTransition`, atau policy setara yang dipanggil semua action.
3. Pindahkan ownership/assignment dari string nama ke user ID dengan migration data yang mempertahankan snapshot nama.
4. Ubah action claim, kembali aset, dan logout dari GET ke POST sesuai route explicit.
5. Batasi controller `Pengguna`, `Stok`, `Vendor`, `KategoriAset`, `KodeKerusakan`, `Aset`, `Kanibal`, dan `Laporan` di server, bukan sidebar.
6. Tambahkan audit/history actor pada assignment, status, lifecycle, stock adjustment, kanibal, loan, return, dan disposal.
7. Tambahkan feature test untuk allow dan deny di setiap row mutation matrix ini.

## 12. Evidence penutupan

F02/F09/F15/F16/F23 baru dapat ditutup setelah ada:

- test matrix tamu, pelapor lain, pelapor pemilik, teknisi tidak ditugaskan, teknisi ditugaskan, dan admin;
- test HTTP method/CSRF untuk setiap mutation yang sebelumnya GET atau form raw;
- test race claim menggunakan dua request/koneksi;
- evidence bahwa account nonaktif tidak dapat melakukan request kritis;
- review response yang membuktikan data LK/asset user lain tidak bocor;
- dokumentasi route, state matrix, dan schema final yang sesuai implementasi.
