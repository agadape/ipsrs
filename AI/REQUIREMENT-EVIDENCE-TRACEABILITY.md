# Requirement-to-Evidence Traceability

Tanggal: **17 September 2026**  
Build evidence baseline: **115 tests / 296 assertions PASS** pada production-hardening release gate 18 September 2026.

Dokumen ini memetakan klaim skripsi ke implementasi dan bukti. Status `Automated` berarti kontrak lokal terbukti; status `Manual pending` berarti browser/perangkat/hosting tetap harus diuji pengguna. Tidak ada baris `Automated` yang boleh dipakai untuk mengklaim production parity.

| Requirement / klaim | Implementasi utama | Evidence otomatis | Evidence manual | Status dan limitation |
| --- | --- | --- | --- | --- |
| Login dan session role | `Auth`, `AuthFilter`, `AccessPolicy` | `AuthLoginHttpTest`, `AuthFilterHttpTest`, `AccessPolicyTest` | Login tiap role dan logout pada hosting | Generic error, inactive revocation, session regeneration, 5/account + 20/IP per 60s throttle, dan headers terbukti; HTTPS/secure-cookie hosting pending |
| Pelapor hanya melihat LK terkait | `LK`, `AccessPolicy::canViewLk()` | `AuthFilterHttpTest`, `AccessPolicyTest` | UAT akun Pelapor | Legacy ownership masih memakai nama display |
| Teknisi hanya mengelola assignment sendiri | `LK`, `AccessPolicy::canManageLk()` | Valid-CSRF detail/parts/vendor/status denial tests | UAT claim dan pekerjaan dua Teknisi | Automated partial; user-ID FK pending |
| Admin-only master/lifecycle/kanibal | `AuthFilter`, controller guards | `AuthFilterHttpTest`, `KanibalTransferTest`, `AssetLoanHttpTest` | UAT menu dan direct URL | Representative routes proven; full browser matrix pending |
| CSRF pada mutation | `Filters`, explicit POST routes | Missing-token and valid-token feature tests | Browser token rotation/back/refresh | Automated representative coverage |
| Pelaporan corrective | `LK`, `LKModel`, state constants | `LkWorkflowContractTest`, timing and HTTP authorization tests | Login → buat LK → claim → survei → perbaikan → selesai | Happy-path browser acceptance pending |
| LK terminal immutable | `LK::updateDetail/addSukuCadang/storeVendor/updateStatus` | Finished-LK valid-CSRF mutation matrix | UAT action visibility/feedback | Automated for representative write routes |
| Response time dan downtime | `LK` timing helpers, `Metrics` | `LkTimingContractTest`, `MetricsTest` | Cocokkan satu laporan/export contoh | Production timezone/sample report pending |
| Preventive LKP dan auto-LK | `Preventif`, `JadwalModel`, `LkpModel` | `LkpChecklistTest`, `PreventifCompletionModelTest`, cross-tech POST denial | `docs/TECHNICAL_UAT_PREVENTIF.md` | SQLite atomic/idempotency proof; MySQL race pending |
| Stock debit/ledger dan rollback LK | `StokModel`, `LK` transactions | `InventoryTransactionModelTest` | UAT penggunaan part dan delete/cancel | MySQL concurrent debit pending |
| Kanibalisasi komponen | `KanibalTransfer`, `Kanibal` | `KanibalTransferTest`, route authorization tests | `docs/TECHNICAL_UAT_KANIBAL.md` | Atomic SQLite proof; formal actor FK pending |
| Lifecycle pinjam/kembali | `Aset`, `AsetLifecycle` | `AssetLoanHttpTest`, `AsetLifecycleTest` | UAT form pinjam/kembali | Happy, invalid-state, validation, duplicate rollback proven; MySQL race pending |
| Mutasi/lokasi aset | `AsetLifecycle`, `Aset::storeMutasi` | `AsetLifecycleTest` | UAT pindah ruangan dan history | Legacy text versus `id_lokasi` reconciliation pending |
| Penghapusan dan BA | `BaDocumentStorage`, `Aset::hapus/downloadBa` | MIME/size/path/name tests; direct-download role denial | Multipart upload/download pada hosting | Local storage safe; cPanel document root pending |
| QR scan dan GPS telemetry | Public scan/ping routes, `Aset::ping` | `PublicPingHttpTest` | QR + izin GPS pada perangkat | Telemetry dapat dipalsukan; bukan bukti lokasi fisik |
| Tanda tangan/serah-terima | `SignatureEvidence`, LK close | `SignatureEvidenceTest` | Canvas/signature browser UAT | Bukti operasional PNG, bukan TTD digital tersertifikasi |
| Laporan periode dan export | `ReportPeriod`, `Laporan`, spreadsheet text helper | `ReportPeriodTest`, `SpreadsheetTextTest` | Buka print/XLSX dan cocokkan sample | Local CLI belum memiliki `ext-zip`; hosting check pending |
| Dependency dan production preflight | Composer lock, `scripts/production_preflight.php` | Composer audit, full PHPUnit, local preflight | Jalankan preflight pada hosting | Local 23 PASS/5 FAIL/3 MANUAL memetakan gap; runtime hosting pending |
| Backup/restore | `scripts/backup_restore_dry_run.php`, deployment runbook | Disposable SQLite restore dan digest PASS | Restore dump MySQL + upload pada staging | Mekanisme lokal terbukti; production-equivalent restore pending |
| Performance daftar/laporan | Current load-all controllers; `scripts/performance_smoke.php` | Synthetic 20k LK benchmark | Remeasure MySQL hosting | Load-all 38.082 ms vs SQL page 2.224 ms (17.1x local); pagination remediation pending |
| User testing | Pertanyaan dan technical UAT docs | N/A | `docs/PERTANYAAN_USER_TESTING.md` dan seluruh technical UAT | Wajib diisi pengguna/responden |

## Release evidence yang boleh diklaim

- Route/filter/controller negative authorization sudah diuji untuk representative high-risk paths.
- Domain contracts untuk LK, preventive, inventory, kanibal, lifecycle, timing, signature payload, report periods, dan safe spreadsheet text memiliki automated regression.
- Local full test suite, route compilation, Composer validation, dan dependency audit menjadi release gate otomatis.
- Login throttle, generic inactive/unknown error, response security headers, dan public PHP surface mempunyai regression proof.
- Backup/restore SQLite disposable dan benchmark sintetis dapat diulang tanpa menyentuh data aplikasi.

## Klaim yang belum boleh dibuat

- “Production-ready sepenuhnya”, sampai hosting parity, backup/restore, real multipart, HTTPS/cookie, dan MySQL concurrency dibuktikan.
- “GPS membuktikan posisi fisik aset”; GPS hanya telemetry perangkat pemindai.
- “Tanda tangan digital tersertifikasi”; implementasi adalah bukti gambar serah-terima operasional.
- “Semua workflow sudah UAT”; checklist browser/perangkat masih harus diisi pengguna.

## Evidence yang harus dikumpulkan pengguna

1. Screenshot dan hasil UAT corrective utama.
2. Hasil UAT preventive dari jadwal sampai LKP dan auto-LK.
3. Hasil UAT kanibal, pinjam/kembali, mutasi, dan BA upload/download.
4. Screenshot export print/XLSX dengan satu data contoh yang diverifikasi.
5. Checklist hosting: PHP/ext-zip, production environment, HTTPS/cookie, writable path, backup dan restore.
6. Jawaban/respon pengguna memakai `docs/PERTANYAAN_USER_TESTING.md`.
