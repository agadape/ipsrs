# IPSRS Schema Baseline and Migration Contract

Tanggal: **15 September 2026**  
Status: **Baseline keputusan untuk Iteration 7; tidak ada migration atau database yang dijalankan saat membuat dokumen ini**  
Dasar: source schema/migration lokal, F20/F21 pada audit, serta logical dump database aktual 15 September 2026. Tidak ada koneksi atau perubahan ke database live dalam tahap ini.

## 1. Kesimpulan baseline

Repository belum mempunyai satu jalur schema yang dapat dianggap authoritative. Dump aktual membuktikan schema deployment telah berevolusi melampaui raw schema repository, tetapi dump tersebut sendiri bukan jalur migration release. Saat ini terdapat:

| Artefak | Kondisi | Status dalam baseline |
| --- | --- | --- |
| `app/Database/Migrations/2026-07-01-092000_buat_tabel_master_data.sql` | Raw SQL, duplikat juga ada dalam directory `migrations` lowercase | Historical bootstrap, bukan CI migration runner. |
| `app/Database/Migrations/2026-07-01-100000_full_mysql_schema.sql` | Schema lama yang masih memodelkan banyak domain ke `aset` parent | Historical reference; tidak boleh dipakai sendirian untuk fresh install target final. |
| `app/Database/Migrations/2026-08-24-094333_AddPeminjamanPenghapusan.php` | Satu migration CI PHP untuk loan/disposal, tanpa foreign key dan bergantung schema sebelumnya | Historical migration; perlu direview/diintegrasikan ke canonical chain. |
| `apply_ddl.php`, `apply_ddl2.php` | Script raw PHP dengan perubahan series/schema dan perubahan data | Manual one-off; bukan jalur release. Tidak dijalankan ulang. |
| `sql/migrate_aset_to_series*.sql`, `sql/lkp_checklist.sql`, `sql/vendor_proses3.sql`, `ERD_FIX*.sql`, `fix_ttd.sql`, `FIX_TTD_COLUMNS.sql` | Patch evolusi yang berbeda tujuan/kualitas; satu file TTD diketahui bermasalah | Evidence evolusi; perlu ditelusuri lalu digantikan migration formal atau diarsipkan. |
| `script_import_excel.php` | Mematikan FK dan truncate aset/series/komponen sebelum import | Utility destructive; bukan setup/migration/fitur produksi. |
| `backup_before_refactor.sql` | Snapshot historis | Tidak menjadi source schema final. |
| `C:\Users\Advan\Downloads\ipsc7141_ipsrs_db (6).sql` | Logical dump MariaDB 10.11.14 tanggal 15 September 2026; mencakup 22 table, data, index, dan FK aktual | **Snapshot schema/data deployment pada waktu dump.** Menjadi input reconciliation, bukan file yang boleh di-import ke database existing karena memiliki `DROP TABLE`. |

Database aktual sudah memiliki `aset_series`, `master_lokasi`, relation series pada LK/LKP/jadwal/kanibal/lifecycle, serta FK penting. Namun tidak ada chain CI migration atau migration-history manifest dalam dump yang dapat mereproduksi evolusi tersebut. Tidak ada perintah schema yang boleh dijalankan pada hosting berdasarkan dokumen ini. Baseline ini menetapkan arah target dan bukti yang diperlukan sebelum Iteration 7 melakukan migration.

### 1.1 Delta schema aktual yang harus dihormati

| Actual dump | Implikasi terhadap baseline target |
| --- | --- |
| `jadwal_preventif.id_aset` ber-FK ke `aset_series.id` | Semantik sudah series; nama kolom legacy perlu migration rename/backward compatibility, bukan perubahan relasi mendadak. |
| LK dan LKP sudah memiliki `id_aset_series` serta FK | Jangan membuat FK baru yang menarget `aset` parent. Tambah ownership/assignment/user ID di atas relation series yang ada. |
| `aset_series.id_lokasi` ber-FK ke `master_lokasi` tetapi kolom lokasi teks lama masih ada | Migration lifecycle/lokasi harus reconcile data lalu menjadikan ID series→lokasi authoritative. |
| `aset_series.status` enum hanya lima nilai lama | State matrix target membutuhkan migration enum/value mapping sebelum writer dapat memakai Menunggu Suku Cadang atau Menunggu Vendor. |
| LKP memiliki `nama_user_ttd` dan `ttd_user`; LK memiliki `ttd_pelapor` | Perbaikan F14/F15 dimulai dari controller policy/persistence; schema extension hanya bila metadata evidence baru diperlukan. |
| Principal FK sudah ada | Jangan menonaktifkan FK untuk "mempermudah" migration/import. Preflight dan sequencing harus mempertahankan referential integrity. |
| Empat history/detail row mempunyai ID string kosong | Data repair wajib didahului mapping reference dan backup; jangan aktifkan UUID validation/unique operation key secara buta. |

## 2. Target authoritative schema path

Target final menggunakan **CodeIgniter PHP migrations yang berurutan di `app/Database/Migrations/`** sebagai satu-satunya jalur schema release.

Aturan directory dan artefak:

1. Setiap perubahan schema/data yang dibutuhkan aplikasi mempunyai migration PHP bernomor waktu dan `up()`/`down()` atau prosedur rollback/restore yang eksplisit jika DDL MySQL tidak reversible.
2. Raw SQL hanya boleh berada di directory `database/reference/`, `database/seed/`, atau `database/archive/` dengan header tujuan, source schema version, dan cara penggunaan. Raw SQL tidak boleh menyamar sebagai migration runner.
3. Tidak ada duplikasi directory `Migrations` versus `migrations`. Canonical target adalah `app/Database/Migrations/` mengikuti namespace CI yang dipakai project.
4. Seed development/test dipisahkan dari migration schema. Seed tidak memuat kredensial produksi dan dapat dijalankan berulang dengan aman.
5. Import data adalah command/utility terpisah dengan backup, preview, validation, mapping stable ID, dan audit log. Ia bukan bagian proses `migrate`.
6. File archive diberi nama jelas dan tidak dirujuk runbook sebagai langkah wajib fresh install.

## 3. Model data authoritative

### A. Identitas aset

| Entity | Identifier | Tanggung jawab |
| --- | --- | --- |
| `aset` | `aset.id` | Katalog/jenis aset: nama, kategori, spesifikasi bersama yang benar-benar sama untuk seluruh unit. |
| `aset_series` | `aset_series.id` | Unit fisik: nomor inventaris, serial, kondisi, availability status, lokasi aktif, identity QR, dan history operasional. Schema aktual sudah memakai model ini. |
| `master_lokasi` | `master_lokasi.id` | Lokasi canonical yang dapat dipilih series; nama/gedung/lantai/ruang tidak diperlakukan sebagai dua sumber kebenaran. |
| `riwayat_lokasi_aset` | `riwayat_lokasi_aset.id` | Event perpindahan series dengan asal/tujuan snapshot serta actor/timestamp. |

Semua relasi operasional yang menyasar satu unit fisik memakai `id_aset_series`, bukan `id_aset` katalog. Nama/nomor snapshot boleh disimpan untuk laporan historis tetapi tidak menggantikan FK authoritative.

### B. Corrective maintenance

| Table | Kunci/relation target | Invarian target |
| --- | --- | --- |
| `laporan_kerusakan` | `id_aset_series` nullable untuk laporan aset tidak teridentifikasi; `id_pengguna_pelapor`; `id_pengguna_teknisi`; optional `source_lkp_id` | `no_order` unique; status canonical; ownership/assignment actor ID; version/timestamps untuk state; no hard delete setelah side effect. |
| `detail_suku_cadang_lk` | `id_lk`; `id_barang` nullable hanya untuk non-gudang; `id_komponen_aset` nullable hanya untuk kanibal; `id_transaksi_stok` untuk Gudang | Satu detail punya source yang jelas. Gudang membutuhkan barang+ledger; Kanibal membutuhkan component/history. |
| `detail_vendor_lk` | `id_lk`; `id_vendor`; actor | Tanggal kirim, estimasi, kembali; return aktual tidak boleh mendahului kirim. |
| LK status history | `id_lk`, actor ID, from/to, reason, timestamp | Setiap transition penting dapat diaudit; target table baru bila tidak dapat direpresentasikan aman pada tabel lama. |

### C. Inventory dan kanibal

| Table | Kunci/relation target | Invarian target |
| --- | --- | --- |
| `barang_persediaan` | `id`, `no_barang` unique | `stok_tersedia >= 0`; saldo diubah hanya oleh operation inventory. |
| `riwayat_transaksi_stok` | `id_barang`, `reference_type`, `reference_id`, `operation_key` unique | Ledger append-only; satu operation tidak menghasilkan debit ganda. |
| `komponen_aset` | `id_aset_series`, quantity, state/component identity | Komponen donor dan penerima terlacak melalui ID stabil, bukan nama substring. |
| `riwayat_kanibal` | donor/penerima `id_aset_series`, `id_lk`, actor/approver ID bila approval dibuat | Donor berbeda dari penerima; relation LK dan component transfer utuh. |

### D. Preventive maintenance

| Table | Kunci/relation target | Invarian target |
| --- | --- | --- |
| `jadwal_preventif` | Existing `id_aset` yang saat ini menunjuk series, lalu target rename-compatible `id_aset_series`; `id_pengguna_teknisi`, category, status canonical | Unit fisik dan teknisi wajib; reschedule/cancel menghasilkan history/relation. |
| `lembar_kerja_preventif` | `id_jadwal`, `id_aset_series`, actor, result, optional signature evidence | Satu final LKP per jadwal kecuali revision workflow formal dibuat. |
| `detail_checklist_lkp` | `id_lkp`, template snapshot, type, raw result/text/value | Tipe `Inspeksi`, `Service`, `Pengukuran`, dan `Teks` dapat disimpan tanpa field hilang. |
| `template_checklist` | category, sequence, item type | Template master tidak ditimpa secara diam-diam oleh hasil inspeksi user. |

### E. Lifecycle and documents

| Table | Kunci/relation target | Invarian target |
| --- | --- | --- |
| `peminjaman_aset` | `id_aset_series`, actor admin, active loan key | Tepat satu loan aktif per series. Status return memiliki timestamp/actor. |
| `penghapusan_aset` | `id_aset_series` unique | Satu record disposal final per series; file BA disimpan melalui metadata/storage policy aman. |
| File BA metadata | disposal ID, storage key, mime, size, checksum, uploader ID, created timestamp | Path client tidak pernah jadi path storage; download melewati authorization. |

## 4. Constraint dan index minimum yang harus ada

| Area | Constraint/index | Tujuan |
| --- | --- | --- |
| User | unique `email`; check/validation role canonical; index `aktif, role` bila query memerlukannya | Identitas login dan role valid. |
| Asset catalog/series | unique `aset_series.nomor_aset`; FK series→aset; FK series→master_lokasi; index availability/status dan location | Unit fisik unik dan terhubung. |
| LK | unique `no_order`; FK LK→series/pelapor/teknisi/source LKP; indexes status/date/series/technician | Permission, dashboard, history, dan report query tepat. |
| Parts | FK parts→LK; FK gudang→barang; FK kanibal→komponen/history; unique `operation_key` atau reference tepat | Sumber parts dan idempotency dapat dibuktikan. |
| Stock | FK ledger→barang; unique `operation_key`; indexes barang/date/reference | Rekonsiliasi dan duplicate prevention. |
| Location | FK history→series dan location source/target; index series/timestamp | History series dapat ditelusuri. |
| PM | FK schedule→series/technician; FK LKP→schedule/series; unique final LKP per schedule; index date/status | LKP tidak duplikat dan scope teknisi benar. |
| Loan | FK loan→series/admin; unique active-loan enforcement | Pinjaman ganda tertolak. |
| Disposal | FK disposal→series/admin; unique disposal series | State terminal terjaga. |

MySQL tidak menyediakan partial unique index seperti beberapa DB lain. Untuk satu loan aktif, gunakan salah satu desain yang diuji:

1. generated column `active_series_id = CASE WHEN status = 'Dipinjam' THEN id_aset_series ELSE NULL END` dengan unique index; atau
2. tabel active allocation terpisah yang dikunci/transaksional.

Unique biasa pada `(id_aset_series, status)` tidak cukup bila history membutuhkan lebih dari satu loan `Selesai`.

## 5. Migration sequence target

Urutan berikut adalah target desain. Nomor/timestamp migration aktual dipilih saat implementasi agar tidak bertabrakan dengan migration repository.

1. **Inventory schema inspection migration:** verifikasi precondition/schema version dan catat database target tanpa destructive DDL.
2. **Canonical identity migration:** validasi existing `master_lokasi` dan `aset_series`; gunakan migration rename-compatible untuk `jadwal_preventif.id_aset`; reconcile location text dengan `id_lokasi`; backfill secara deterministic dengan report unresolved rows.
3. **Operational relation migration:** tambah ID actor/series untuk LK, PM, history, kanibal, loan, dan disposal; backfill snapshot berdasarkan mapping yang dapat diaudit.
4. **Status transition migration:** tambah vocabulary/status/history/version yang diperlukan; map nilai legacy melalui tabel mapping; tolak nilai tak dikenal.
5. **Inventory integrity migration:** tambah reference/operation key, component relation, constraints/index, dan reconciliation report sebelum enforcement penuh.
6. **Preventive workflow migration:** tambah LKP relation/result/text/unique final record dan schedule transition support.
7. **File/document migration:** pindahkan metadata BA dari path legacy ke storage key policy; jangan memindahkan file production tanpa backup/verification.
8. **Enforcement migration:** aktifkan FK/unique/check setelah preflight menunjukkan data lama memenuhi invariants atau telah diputuskan remediation-nya.
9. **Cleanup migration:** drop/rename legacy columns hanya setelah release yang menggunakan canonical column stabil dan rollback window berakhir.

Sebelum langkah 1, jalankan preflight dump/live pada `AI/DB-DUMP-ADDENDUM-2026-09-15.md`. Tidak ada migration yang boleh memperbaiki empat empty-string ID tanpa mapping relasi dan backup yang diverifikasi.

Setiap migration MySQL yang memiliki implicit commit harus memiliki backup/restore checkpoint; `down()` tidak boleh berpura-pura reversible bila data sudah dihapus atau diubah tak dapat dipulihkan.

## 6. Fresh install, upgrade, dan rollback contract

### Fresh install

1. Buat database MySQL kosong dengan charset/collation yang ditetapkan release.
2. Jalankan canonical CI migrations satu kali dari awal.
3. Jalankan seed non-production yang eksplisit bila environment development/test.
4. Jalankan schema verification: daftar migration, table, FK, unique/index, role/status valid, dan test smoke.
5. Jangan menjalankan `apply_ddl*.php`, `script_import_excel.php`, atau raw patch archive sebagai langkah setup.

### Upgrade existing deployment

1. Ambil backup database dan file BA, verifikasi backup dapat dibuka pada environment terpisah.
2. Jalankan preflight read-only: schema version, row count, legacy status/location/ID yang tidak dapat dimap, orphan relation, duplicate loan/operation key, dan disk storage BA.
3. Tinjau preflight; jangan menjalankan migration jika unresolved data dapat melanggar constraint baru.
4. Aktifkan maintenance window bila migration mengubah tabel besar/operasional.
5. Jalankan migration canonical sekali; simpan output, commit release, waktu, operator, dan schema version.
6. Jalankan post-migration reconciliation dan smoke test role/workflow tanpa destructive data test.
7. Bila failure terjadi sebelum non-reversible step, gunakan down/transaction yang telah diuji. Bila setelah non-reversible step, restore backup sesuai runbook; jangan melakukan patch ad hoc langsung di production.

### Rollback

Rollback berarti memulihkan source release sebelumnya **dan** database/file backup yang konsisten bila migration tidak reversible. Rollback source tanpa schema compatibility review dilarang.

## 7. Import policy

`script_import_excel.php` tidak boleh dijalankan pada database berisi data operasional karena mematikan FK dan truncate aset/series/komponen. Kebijakan final:

- Untuk development demo kosong: utility hanya dapat dipakai pada database disposable yang dibuat khusus, dengan warning dan confirmation command eksplisit.
- Untuk data existing: buat importer baru yang preview-only terlebih dahulu, memvalidasi semua row, memakai stable ID/upsert yang disetujui, melaporkan error per row, dan tidak truncate tanpa backup/approval.
- Import bukan cara melakukan migration schema atau memperbaiki data partial akibat deployment.

## 8. Test evidence sebelum schema dianggap final

- Fresh install MySQL disposable dari zero sampai aplikasi dapat boot dan test domain lulus.
- Upgrade fixture schema lama yang representatif, termasuk data legacy status/lokasi/LK/stock/loan, lalu preflight dan migration result diperiksa.
- Constraint test: duplicate number/order, active loan ganda, final LKP ganda, ledger operation duplicate, FK invalid, status invalid.
- Transaction test: debit stock/detail/ledger rollback, location/history rollback, LKP/auto-LK rollback.
- Reconciliation query tersimpan untuk saldo vs ledger, status asset vs LK/loan/disposal, location active vs history, dan orphan FK.
- Backup/restore test pada target yang terpisah dari database asal.

## 9. Artefak yang harus diperbarui saat Iteration 7

1. `README.md`: satu cara setup dari database kosong, test command, limitation, dan environment variables tanpa secret.
2. `AI/SCHEMA-BASELINE.md`: ganti status target menjadi actual schema version serta link migration final.
3. ERD final dari migration/schema final, bukan dari screenshot atau SQL historis.
4. Runbook deploy/restore berisi source tag, schema version, backup/restore, verification, dan rollback decision.
5. `PROJECT-AUDIT-REPORT.md`: update status F20/F21 dengan commit, test, migration evidence, dan residual risk.
