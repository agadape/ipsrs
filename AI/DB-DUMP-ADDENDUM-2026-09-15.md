# Database Dump Addendum — 15 September 2026

## 1. Source, scope, and limitations

Source reviewed: `C:\Users\Advan\Downloads\ipsc7141_ipsrs_db (6).sql`.

The file is a logical MariaDB dump generated on **15 September 2026 at 19:58**, reports MariaDB **10.11.14**, and declares database `ipsc7141_ipsrs_db`. It contains `DROP TABLE`, `CREATE TABLE`, data inserts, indexes, and foreign keys for 22 application tables.

This is stronger evidence than repository SQL snapshots because it describes one real database state. It is still not a live database connection: it does not prove the database has remained unchanged after the dump time, it does not expose server SQL mode/runtime configuration, and it must never be imported into an existing database because its `DROP TABLE` statements are destructive.

The dump contains personal data and password hashes. It must remain outside Git, source archives, public chat, and public storage. This addendum intentionally records no credentials, user identity, asset detail, or operational free text from the dump.

## 2. Confirmed actual schema

The dump confirms the current database already includes:

- `aset` as the catalog and `aset_series` as the physical-unit table.
- `master_lokasi` and a foreign key from `aset_series.id_lokasi` to it.
- `laporan_kerusakan.id_aset_series` and a foreign key to `aset_series`.
- `lembar_kerja_preventif.id_aset_series` and a foreign key to `aset_series`.
- `jadwal_preventif.id_aset`, whose name is legacy but whose foreign key points to `aset_series.id`.
- `komponen_aset.id_aset_series`, `riwayat_kanibal.id_series_donor`, and `riwayat_kanibal.id_series_penerima`, all related to physical series.
- `peminjaman_aset` and `penghapusan_aset`, both referencing `aset_series` and an optional admin user.
- `ttd_pelapor` in LK and `ttd_user` plus `nama_user_ttd` in LKP.
- FK/index coverage for principal LK detail, vendor detail, stock ledger, location history, LKP checklist, series, loan, disposal, and kanibal relations.

Therefore the project has already migrated further than the repository's initial raw schema scripts. The dump does not contain a CI migration-history table or another release manifest that would reconstruct this state from a database empty of data.

## 3. Material differences from the initial audit baseline

| Topic | Initial audit position | Evidence from dump | Updated conclusion |
| --- | --- | --- | --- |
| Production-like schema | Not directly verified; repository scripts were ambiguous | Full logical dump with 22 tables, indexes, FKs, and data | Actual schema at dump time is now known. Reproducibility remains unresolved. |
| Series migration | Some source/view/script paths still appeared parent-oriented | LK, LKP, PM schedule, kanibal, location, loan, and disposal are substantially linked to `aset_series` | Physical-series direction is correct in DB; repository migration history and some source terminology are behind it. |
| Location | Two-source risk identified | `id_lokasi` FK exists while legacy text location/gedung/lantai/room/unit remains in `aset_series` | F12 is confirmed, not merely suspected. FK protects location ID validity but does not resolve competing displayed values/history bypass. |
| LKP TTD/name | Missing persistence was suspected | Columns already exist in actual LKP table | F14 is confirmed as application persistence/whitelist behavior, not a missing-column problem. |
| Kanibal relation | Linkage was thought incomplete | Donor/penerima series and LK number FK are present | F11 remains: component is free text, no stable component reference on LK detail, quantity/approval workflow remain incomplete. |
| Lifecycle status | Config/source used inconsistent strings | `aset_series.status` enum allows only `Tersedia`, `Dipinjam`, `Dalam Perbaikan`, `Rusak Berat`, `Dihapuskan`; LK records use workflow statuses outside that enum | F10 is strengthened. Any writer that maps to `Menunggu Suku Cadang`, `Menunggu Vendor`, `Aktif`, or `Tidak Aktif` can fail in strict SQL mode or be coerced in permissive mode. |
| Ownership/assignment | Actor identities were name-based in source | LK/LKP/schedule store display strings; no `id_pengguna_pelapor`, `id_pengguna_teknisi`, or schedule technician user FK exists | F02/F09 remain unchanged and require schema support in a later migration. |
| Stock idempotency | No operation key was found in source | Ledger lacks `operation_key`, immutable source relation, and unique operation constraint | F05/F06 remain unchanged. Existing FK does not prevent duplicate debit/reversal. |
| Migration path | Multiple raw SQL/manual patches found | Actual schema contains evolved changes not reproduced by one canonical migration path | F20 becomes schema-drift/reproducibility finding, still P1. |

## 4. Confirmed data-quality preflight issues

The dump contains **four rows with an empty string primary-key value** across history/detail tables: one LKP checklist detail, one LK parts detail, one location-history row, and one stock-ledger row. Empty string is accepted by a `CHAR(36) NOT NULL` primary key as a value, but it is not a valid application UUID and can break future UUID assumptions, reconciliation, references, or idempotency migrations.

This becomes **F37 — Existing historical rows use empty-string identifiers**.

- **Severity:** P2.
- **Impact:** A future migration that adds references, validates UUID format, reconstructs history, or creates unique operation keys can silently omit or mis-handle these rows. The dump alone does not show active user failure.
- **Required response:** Run a read-only preflight over the live database; map every empty ID and its inbound/outbound references; create a backup; repair IDs through an audited migration only after the mapping is reviewed; run reconciliation afterward.

Other observed historical values appear test-like or operationally unusual, including dates far outside the current operating period and nullable asset links in older LKP rows. These are **preflight flags**, not separate defects yet: their business meaning must be checked with the project owner before data is normalized or deleted.

## 5. Revised pre-Iteration 1 checkpoint

Iteration 0.5 is complete for the supplied dump. Before the first schema-affecting release, run this read-only preflight on the live database and save aggregate results without exposing personal data:

1. `SELECT` schema metadata from `information_schema` and compare table/column/index/FK definitions to the 15 September dump.
2. Count empty/non-UUID primary IDs by table and map FK references to them.
3. Count asset series by lifecycle status and identify values not in the approved state matrix.
4. Identify series whose legacy text location differs from `master_lokasi` resolved text.
5. Identify active loans per series, disposal records per series, and active LK per series that violate target lifecycle rules.
6. Reconcile `barang_persediaan.stok_tersedia` with stock ledger using the agreed opening-balance and reversal rules; do not infer a discrepancy before those rules are fixed.
7. Identify LK/LKP rows lacking series or user-identity relations and classify them as legitimate historical/manual records versus migration candidates.
8. Verify the dump is retained as a protected backup and test restoration only on a disposable database.

## 6. Effect on execution order

The dump does **not** justify skipping Iteration 1. BA upload, CSRF/method consistency, XSS, and spreadsheet formula injection are application security problems independent of the existing series migration.

The revised order is:

1. Record this database evidence and complete the Iteration 0.5 schema/data contract.
2. Begin Iteration 1A upload security and 1B/1C request/output security.
3. Use the actual dump schema, rather than repository raw SQL, as the input when Iteration 2 adds ownership/assignment and Iteration 4 reconciles lifecycle/location.
4. Require the live preflight and backup before Iteration 7 runs any migration or data repair.

## 7. Evidence references

The evidence is retained in the supplied dump at the following schema sections: `aset_series` around lines 73–99, LK around 1580–1604, LKP around 1629–1642, master lokasi around 1663–1670, principal indexes around 2723–2892, and foreign keys around 2902–2991. These line references describe the dump reviewed on 15 September 2026, not a guarantee about later live database state.
