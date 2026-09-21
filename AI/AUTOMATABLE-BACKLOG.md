# Backlog Pekerjaan yang Dapat Dieksekusi Agen

Tanggal pembaruan: **17 September 2026**  
Baseline saat dokumen dibuat: **92 PHPUnit tests / 186 assertions PASS**, CodeIgniter 4.7.4, Composer audit bersih.  
Sumber kebenaran: `PROJECT-AUDIT-REPORT.md`, `PROJECT-REMEDIATION-PLAN.md`, `PROJECT_CONTEXT.md`, dan source tree saat ini.

**Automated phase closure — updated 18 September 2026:** a user-authorized production-hardening pass followed the original closure. Current local baseline is **115 tests / 296 assertions PASS**; routes, Composer validation, and locked advisory audit pass. Login throttling, response headers, public-surface cleanup, production preflight, disposable recovery drill, and performance evidence were added. Remaining browser, device, hosting, live MySQL, and user-acceptance items are evidence gates rather than open-ended autonomous coding.

Dokumen ini menjawab pertanyaan praktis: **apa lagi yang dapat dikerjakan agen tanpa menunggu UAT browser atau mengubah data hosting?** Ini bukan janji bahwa semua item perlu dikerjakan sebelum demo; prioritas mengikuti dampak terhadap keamanan, alur inti, dan bukti skripsi.

## Aturan eksekusi

1. Setiap perubahan source harus sempit, memiliki test yang langsung membuktikan kontraknya, lalu menjalankan full PHPUnit.
2. Status finding hanya menjadi `Verified` bila bukti sesuai definition of done; perbaikan source tanpa bukti browser/MySQL/hosting tetap `partially verified`.
3. Tidak ada import dump, migration pada hosting, upload ke hosting, atau mutasi data nyata tanpa instruksi eksplisit pengguna.
4. Jangan mengganti arsitektur MVC sederhana hanya untuk memenuhi checklist. Fokus pada rule server-side dan integritas data.
5. Setelah setiap item, perbarui tiga dokumen AI dan tambahkan iteration log baru; jangan menulis ulang riwayat iterasi lama.

## A. Dapat dikerjakan sekarang — prioritas tertinggi

### A1. Perluas HTTP authorization untuk mutasi LK

- **Status 17 September 2026:** Implemented — partially verified. Valid-CSRF request tests now cover foreign-Technician detail/parts/vendor denial and Pelapor delete denial; baseline after this slice is **94 tests / 197 assertions PASS**.
- **Finding:** F02, F09, F16.
- **Yang dapat dikerjakan:** Tambah feature test SQLite terhadap route nyata untuk request valid-CSRF berikut: Teknisi mencoba mengubah detail/status/parts/vendor LK milik Teknisi lain; Pelapor mencoba detail/status/delete; Teknisi mencoba delete LK. Pastikan redirect/denial dan record tidak berubah.
- **Mengapa penting:** Current tests sudah membuktikan deny untuk claim Pelapor dan delete jadwal oleh Teknisi, tetapi belum membuktikan semua mutasi LK yang paling sering dipakai.
- **Evidence selesai:** Test menembus CSRF + `AuthFilter` + controller; query setelah request membuktikan tidak ada perubahan data. Full suite lulus.
- **Batas:** Ownership masih memakai nama display, bukan `user_id`; test ini tidak menghapus debt schema tersebut.
- **Kompleksitas:** Sedang.

### A2. Tutup HTTP contract pada preventive LKP submission

- **Status 17 September 2026:** Implemented — partially verified. Valid-CSRF cross-Technician POST denial preserves target schedule status; baseline after this slice is **95 tests / 200 assertions PASS**.
- **Finding:** F02, F13, F14, F16.
- **Yang dapat dikerjakan:** Tambah feature test valid-CSRF bahwa Teknisi lain tidak dapat `POST /ipsrs/preventif/lkp/{jadwal}`; jadwal tetap `Belum` dan LKP tidak dibuat. Tambah satu happy-path HTTP integration pada SQLite bila schema test cukup, atau pertahankan database-contract test bila route/render dependency terlalu besar.
- **Mengapa penting:** Guard GET form dan hasil LKP sudah terbukti; endpoint POST adalah batas data paling penting.
- **Evidence selesai:** Denial lintas-teknisi tidak mengubah jadwal/LKP; duplicate submit hanya menghasilkan satu LKP.
- **Batas:** MySQL multi-connection race masih memerlukan environment MySQL terpisah.
- **Kompleksitas:** Sedang–tinggi.

### A3. Perkuat akses BA upload/download secara request-level

- **Status 17 September 2026 (latest):** Local storage contract proven with content-derived PNG extension, random key, non-public resolved path, and cleanup; real multipart/hosting proof remains manual.
- **Status 17 September 2026:** Partially verified. Teknisi direct-download denial now has HTTP proof; multipart/storage/download-hosting proof remains open.
- **Finding:** F01, F02.
- **Yang dapat dikerjakan:** Review `Aset` upload/download BA lalu tambah feature test untuk anonymous/Pelapor/Teknisi denial, Admin allow path pada file sementara, file hilang, MIME/ukuran/file-name policy. Perbaiki hanya bila test menemukan gap nyata.
- **Mengapa penting:** Ini satu-satunya finding P0 yang belum memiliki proof HTTP multipart dan web-like storage.
- **Evidence selesai:** Test upload multipart yang aman di temporary writable directory; file disimpan di lokasi non-public; download memakai `Content-Disposition: attachment`; role salah tidak menerima file.
- **Batas:** Tidak dapat membuktikan cPanel document root, `.htaccess`, antivirus, atau web server production tanpa hosting.
- **Kompleksitas:** Tinggi.

### A4. QR/public GPS ping contract dan telemetry clarity

- **Status 17 September 2026 (latest):** Backend local contract complete for valid write, invalid range, unknown series, and rate-limit no-overwrite. UI distinguishes `updated=false`; browser/device evidence remains.
- **Status 17 September 2026:** Partially verified. Canonical actor key fixed and valid-CSRF JSON persistence has feature proof; telemetry spoofing, rate-limit UX, and device evidence remain open.
- **Finding:** F18, F24.
- **Yang dapat dikerjakan:** Perbaiki `last_seen_by` agar memakai session key canonical `user_name`; ubah respons rate-limit agar tidak mengklaim lokasi baru tersimpan; tambah test JSON untuk malformed payload, out-of-range coordinates, asset tidak ada, valid data, dan rate-limit. Audit halaman scan agar detail berasal dari `aset_series`, bukan katalog parent bila masih ada mismatch.
- **Mengapa penting:** Endpoint publik boleh menjadi telemetry, tetapi UI tidak boleh menyatakan kepastian lokasi yang tidak disimpan atau dapat diverifikasi.
- **Evidence selesai:** Kontrak 400/404/200 dan perubahan/tidak-berubah kolom last-seen terbukti pada SQLite; dokumentasi menyebut GPS sebagai telemetry yang dapat dipalsukan.
- **Batas:** GPS spoofing/radius/akurasi fisik tidak dapat dieliminasi hanya dengan backend; perlu limitation di UI dan UAT perangkat.
- **Kompleksitas:** Sedang.

### A5. LK state-machine negative matrix

- **Status 17 September 2026:** Implemented — partially verified. Completed LK rejects detail/parts/vendor/status reopening with valid-CSRF persistence assertions; other illegal transitions and successful close edge cases remain.
- **Finding:** F09, F17.
- **Yang dapat dikerjakan:** Tambah test untuk state illegal, second claim, update/parts/vendor setelah `Selesai`, close tanpa assignment/tindakan/signature, timestamp survei tidak valid, dan request ulang. Perbaiki hanya state guard yang terbukti masih lolos.
- **Mengapa penting:** LK adalah alur utama skripsi: laporan → claim → survei → perbaikan → selesai.
- **Evidence selesai:** Matrix state/action/role tercermin dalam test otomatis dan error tidak mengubah status/data.
- **Batas:** Actor identity tetap legacy display-name sampai ada migration user-ID.
- **Kompleksitas:** Sedang.

## B. Dapat dikerjakan sekarang — reliabilitas/domain

### B1. Lifecycle asset request-level tests

- **Status 17 September 2026 (latest):** Local gate complete for borrow/return happy paths, required input, invalid state, and duplicate-active rollback. MySQL concurrency/browser evidence remains.
- **Status 17 September 2026:** In progress. Borrow/return fatal namespace defects are fixed and valid-CSRF Admin happy paths have atomic SQLite persistence proof; negative lifecycle and concurrency evidence remain.
- **Finding:** F07, F10, F12.
- **Yang dapat dikerjakan:** Tambah integration tests untuk pinjam/return/mutasi/tandai rusak/disposal dengan state salah, record loan aktif, lokasi invalid, dan asset terminal. Pastikan semua side effect rollback pada kegagalan.
- **Evidence selesai:** HTTP atau database contract yang membuktikan lifecycle tidak dapat dilompati dan history konsisten.
- **Batas:** Reconciliation data legacy serta MySQL concurrency masih terpisah.
- **Kompleksitas:** Tinggi.

### B2. Kanibal request-level dan rollback proof

- **Finding:** F11.
- **Yang dapat dikerjakan:** Feature test admin vs non-admin, POST yang dimanipulasi (`no_order_lk`, approver, donor/recipient), dan forced failure setelah salah satu write. Tambah assertion bahwa semua side effect rollback.
- **Evidence selesai:** HTTP valid-CSRF tidak mengizinkan non-Admin; server-derived actor/LK terbukti; transaksi tetap atomik.
- **Batas:** Actor FK/approval formal dan MySQL lock dua koneksi masih belum selesai.
- **Kompleksitas:** Tinggi.

### B3. Inventory dan delete-LK failure matrix

- **Finding:** F05, F06.
- **Yang dapat dikerjakan:** Tambah test failure injection untuk detail part, ledger, dan delete LK; test duplikasi Gudang/kanibal pada data nyata test; periksa bahwa penghapusan tidak mengembalikan stock `Kanibal`.
- **Evidence selesai:** Tidak ada saldo/ledger/detail setengah jadi pada semua kegagalan yang dapat disimulasikan.
- **Batas:** SQLite tidak membuktikan concurrent debit MySQL.
- **Kompleksitas:** Sedang.

### B4. Vendor lifecycle and close guard

- **Finding:** F27, F09.
- **Yang dapat dikerjakan:** Audit status vendor dan form; tambah route/controller untuk catat pengiriman dan kepulangan vendor pada entry yang sama bila benar-benar missing; blokir close LK ketika pekerjaan vendor masih aktif; tambah test chronology invalid.
- **Evidence selesai:** Timeline vendor tidak dapat berada pada urutan tanggal/status yang tidak masuk akal, dan LK tidak selesai prematur.
- **Batas:** Perlu menahan scope bila workflow vendor tidak termasuk requirement skripsi yang diklaim.
- **Kompleksitas:** Sedang.

### B5. Sequence allocator boundary/collision tests

- **Finding:** F32.
- **Yang dapat dikerjakan:** Test nomor LK/LKP/stock pada rollover batas digit/tahun/bulan dan duplicate collision retry; sesuaikan helper jika ada defect aktual.
- **Evidence selesai:** Allocator tidak mengeluarkan nomor ambigu pada batas yang dicakup kontrak.
- **Batas:** Unique index database final masih bagian F20.
- **Kompleksitas:** Rendah–sedang.

## C. Dapat dikerjakan sekarang — security, error, and output

### C1. Login/session/error hardening review

- **Finding:** F23, F28.
- **Yang dapat dikerjakan:** Audit login rate limiting, generic error responses, session fixation/regeneration, logout, inactive-account behavior, `Cookie`/HTTPS config, and error logging. Tambah local test untuk login attempts/inactive state bila limiter ada atau implementasikan limiter yang sederhana dan terdokumentasi.
- **Evidence selesai:** Password guessing tidak menghasilkan error account-enumeration; inactive session ditolak; login limit memiliki test; production config requirement tercatat.
- **Batas:** HTTPS, secure-cookie, server error page, log rotation, dan WAF hanya dapat diverifikasi pada hosting.
- **Kompleksitas:** Sedang.

### C2. CSRF valid-token route sampling

- **Finding:** F03, F16.
- **Yang dapat dikerjakan:** Perluas test dari no-token denial ke valid-token paths untuk representative mutation per module: asset, LK, PM, stock, master, kanibal, logout. Fokus deny actor/state tanpa menjalankan mutation yang tidak perlu.
- **Evidence selesai:** Test membedakan CSRF denial dari authorization/state denial.
- **Batas:** Browser token rotation/back-refresh masih UAT.
- **Kompleksitas:** Sedang.

### C3. Dynamic DOM/XSS and Excel regression coverage

- **Finding:** F19, F34.
- **Yang dapat dikerjakan:** Static sink scan, unit test helper encoding, XLSX cell-type/content contract jika `ext-zip` tersedia, dan print/export fallback review. Perbaiki sink nyata saja.
- **Evidence selesai:** Input formula/HTML tidak menjadi formula/script di output yang diuji.
- **Batas:** CLI saat ini tidak memiliki `ext-zip`, jadi XLSX writer runtime tidak dapat dibuktikan lokal.
- **Kompleksitas:** Rendah–sedang.

### C4. Public route minimization and documentation consistency

- **Finding:** F02, F16, F36.
- **Yang dapat dikerjakan:** Static inventory seluruh route public vs route group; test anonymous redirect untuk internal resource penting; hapus only dead exceptions. Selaraskan `PROJECT_CONTEXT`, access matrix, deployment checklist, README, and guide terhadap source aktual.
- **Evidence selesai:** Tidak ada route internal yang tidak sengaja bypass auth; docs tidak membuat klaim CSRF/auth yang bertentangan.
- **Batas:** Tidak mengganti policy produk yang memang meminta portal/QR publik.
- **Kompleksitas:** Sedang.

## D. Dapat dikerjakan sekarang — UX, reports, thesis evidence

### D1. Form recovery/error-state code review

- **Finding:** F29, F03, F04.
- **Yang dapat dikerjakan:** Audit form yang kehilangan `old()` data, modal yang mem-bypass `reportValidity()`, action dynamic, empty/error/success feedback. Perbaiki defects yang terbukti melalui rendered HTML/static JS test.
- **Evidence selesai:** Validation failure mempertahankan input utama dan submit tidak bypass browser/API validation.
- **Batas:** Responsiveness, visual hierarchy, browser back, double click, dan mobile tetap perlu browser UAT.
- **Kompleksitas:** Sedang.

### D2. Report/output correctness contracts

- **Finding:** F17, F25, F26, F34.
- **Yang dapat dikerjakan:** Tambah test fixture untuk week/month/year, RT=0, terminal LK, PM source date, labels print, and formula-like values. Inspect query count/shape statically and only optimize measured N+1.
- **Evidence selesai:** Data period dan output label konsisten dengan contract; known limitation snapshot historis tertulis.
- **Batas:** Spreadsheet visual, real dataset performance, and hosted `ext-zip` require separate evidence.
- **Kompleksitas:** Sedang.

### D3. Requirement-to-evidence traceability pack

- **Finding:** F35, F36; thesis readiness.
- **Yang dapat dikerjakan:** Buat matrix requirement → route/controller/model → automated test → manual UAT case → screenshot/evidence → limitation. Selaraskan terminology preventive/corrective, TTD evidence, GPS telemetry, response time, kanibal, mutasi, and reports.
- **Evidence selesai:** Penguji dapat menelusuri klaim skripsi ke implementasi dan test/UAT tanpa mengandalkan narasi lisan.
- **Batas:** Screenshot hasil UAT dan acceptance actual tetap dari pengguna/penguji.
- **Kompleksitas:** Sedang.

### D4. Accessibility/static usability pass

- **Finding:** F31, F33.
- **Yang dapat dikerjakan:** Audit labels, duplicate IDs, keyboard modal, focus trap, button types, table headers, contrast hints, unused code/legacy calls. Terapkan patch kecil yang tidak mengubah alur bisnis.
- **Evidence selesai:** Static/accessibility checks dan targeted rendering tests lulus.
- **Batas:** Keyboard/mobile assistive-tech proof butuh browser/device.
- **Kompleksitas:** Sedang.

## E. Dapat dikerjakan, tetapi perlu keputusan scope atau lebih berisiko

### E1. Formal migration/reproducible schema work

- **Finding:** F20, F37.
- **Yang dapat dikerjakan:** Inventaris migration repository, buat schema preflight command read-only untuk target database, desain migration forward/rollback dan data-repair plan untuk empty IDs.
- **Tidak akan dilakukan otomatis:** Menjalankan DDL/data repair pada hosting atau mengimport dump.
- **Kebutuhan keputusan:** Canonical schema version, backup/restore, downtime policy, mapping inbound references untuk four empty-string IDs.
- **Kompleksitas:** Tinggi.

### E2. Import/reset legacy script retirement

- **Finding:** F21.
- **Yang dapat dikerjakan:** Klasifikasikan script destruktif, tambahkan guard environment/confirmation/dry-run atau deprecate dari runbook, lalu test bahwa script tidak berjalan terhadap production config secara tidak sengaja.
- **Kebutuhan keputusan:** Apakah import Excel masih termasuk scope final atau dicabut dari klaim sistem.
- **Kompleksitas:** Sedang–tinggi.

### E3. Notification/WhatsApp scope decision

- **Finding:** F22.
- **Yang dapat dikerjakan:** Pisahkan adapter dari business transaction, tambah status failure yang tidak menggagalkan LK, atau hapus klaim UI/docs jika memang placeholder.
- **Tidak dapat dikerjakan sepenuhnya tanpa:** Credentials/API provider/nomor tujuan dan izin mengirim pesan eksternal.
- **Kompleksitas:** Sedang.

### E4. Performance benchmark harness

- **Finding:** F26.
- **Yang dapat dikerjakan:** Buat fixture dataset dan query-count/time harness untuk daftar/export. Patch pagination/projection hanya jika ada hasil ukur yang nyata.
- **Batas:** Angka SQLite lokal bukan representasi hosting MySQL atau dataset rumah sakit sebenarnya.
- **Kompleksitas:** Sedang.

## F. Tidak dapat diselesaikan agen sendiri

| Item | Bukti yang diperlukan | Mengapa membutuhkan pihak lain |
| --- | --- | --- |
| UAT browser workflow utama | Login → buat LK → claim → survei → perbaikan → selesai; PM → LKP → auto-LK; vendor, kanibal, lifecycle | Membuktikan UI nyata, token browser, feedback, dan penerimaan pengguna |
| UAT perangkat QR/GPS | Kamera, QR cetak, izin lokasi, GPS/iPhone/Android | Memerlukan perangkat/fisik lokasi |
| Hosting/deployment parity | PHP `ext-zip`, `CI_ENVIRONMENT=production`, HTTPS, secure cookie, upload path, `.htaccess`, backup/restore | Agen tidak boleh mengubah/mengecek hosting tanpa akses/instruksi |
| MySQL concurrency | Dua koneksi MySQL untuk stock/claim/LKP/kanibal | SQLite unit tests tidak mewakili locking MySQL |
| Live data preflight/repair | F37 ID kosong, schema drift, referensi existing | Berisiko terhadap data produksi; perlu backup dan owner data |
| Integrasi WhatsApp | Credential, sandbox/prod account, recipient permission | Aksi eksternal memerlukan otorisasi eksplisit |
| Bukti skripsi akhir | Screenshot, respon user testing, keputusan pembimbing, sign-off limitation | Hanya pengguna/penguji dapat memberi acceptance |

## Urutan yang direkomendasikan dari titik sekarang

Status 18 September 2026: A1, A2, local portion A3–A5, dan D3 sudah memiliki implementation/evidence yang dicatat pada Iteration 01O–01Y. Urutan aktif sekarang:

1. Jalankan `php scripts/production_preflight.php` pada hosting dan tutup seluruh baris `FAIL`.
2. Jalankan **UAT browser** memakai dokumen di `docs/`: corrective, preventive, kanibal, lifecycle, BA, dan laporan/export.
3. Jalankan QR/GPS pada perangkat nyata serta multipart BA dan XLSX pada hosting.
4. Lakukan backup/restore staging dan MySQL concurrency proof bila project akan diklaim production-ready.
5. Isi `docs/PRODUCTION_RELEASE_RECORD.md`, lampirkan screenshot/evidence, lalu tetapkan `GO`, `DEMO ONLY`, atau `NO-GO`.
6. Perbaiki hanya defect yang ditemukan dari gate tersebut. Pagination F26 menjadi patch berikutnya bila dataset/MySQL mengonfirmasi dampaknya.
7. E1/E2/E3 (migration/import/notification) tetap deferred sesuai keputusan scope sebelumnya.

## Hal yang sengaja tidak diprioritaskan sekarang

- Redesign visual besar, karena UI sudah cukup untuk validasi workflow dan perubahan visual berisiko menambah regression.
- Micro-optimization tanpa measurement nyata.
- Migrasi identitas actor dari nama display ke FK `user_id` tanpa baseline schema/back-up; itu benar secara desain tetapi berisiko tinggi menjelang deadline.
- Fitur baru seperti notifikasi real-time, scheduler otomatis, atau approval kriptografis bila tidak menjadi requirement final yang akan diklaim.

## Definition of progress

Pekerjaan dianggap benar-benar maju bila satu item menghasilkan minimal satu dari berikut:

- vulnerability/bug nyata ditutup dengan regression test;
- rule server-side penting mendapat evidence HTTP/database;
- workflow memiliki UAT case dan hasil yang dapat dilacak;
- dokumentasi klaim skripsi menjadi lebih akurat terhadap implementasi;
- release/deployment risk berubah dari asumsi menjadi evidence.

Menambah test atau dokumentasi yang hanya mengulang implementasi tanpa menjawab risiko baru bukan prioritas.
