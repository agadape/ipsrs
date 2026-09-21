<?php

use App\Libraries\KanibalTransfer;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/** @internal */
final class KanibalTransferTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $prefix = $this->db->getPrefix();
        foreach (['detail_suku_cadang_lk', 'riwayat_kanibal', 'komponen_aset', 'laporan_kerusakan', 'aset_series'] as $table) {
            $this->db->query('DROP TABLE IF EXISTS ' . $prefix . $table);
        }
        $this->db->query('CREATE TABLE ' . $prefix . 'aset_series (id TEXT PRIMARY KEY, status TEXT NOT NULL)');
        $this->db->query('CREATE TABLE ' . $prefix . 'laporan_kerusakan (id TEXT PRIMARY KEY, no_order TEXT NOT NULL, id_aset_series TEXT, status TEXT NOT NULL)');
        $this->db->query('CREATE TABLE ' . $prefix . 'komponen_aset (id TEXT PRIMARY KEY, id_aset_series TEXT NOT NULL, nama_komponen TEXT NOT NULL, kondisi TEXT NOT NULL, asal TEXT, id_riwayat_kanibal TEXT, tanggal_dicatat TEXT, keterangan TEXT)');
        $this->db->query('CREATE TABLE ' . $prefix . 'riwayat_kanibal (id TEXT PRIMARY KEY, no_order_lk TEXT NOT NULL, id_series_donor TEXT, id_series_penerima TEXT, nama_komponen TEXT NOT NULL, kondisi_komponen TEXT, tanggal TEXT, petugas TEXT, disetujui_oleh TEXT, keterangan TEXT)');
        $this->db->query('CREATE TABLE ' . $prefix . 'detail_suku_cadang_lk (id TEXT PRIMARY KEY, id_lk TEXT NOT NULL, id_barang TEXT, sumber TEXT NOT NULL, nama_barang TEXT, jumlah INTEGER NOT NULL, satuan TEXT, keterangan TEXT)');
        $this->db->table('aset_series')->insertBatch([
            ['id' => 'donor', 'status' => 'Rusak Berat'],
            ['id' => 'receiver', 'status' => 'Dalam Perbaikan'],
        ]);
        $this->db->table('laporan_kerusakan')->insert(['id' => 'lk-1', 'no_order' => 'LK-1', 'id_aset_series' => 'receiver', 'status' => 'Dalam Perbaikan']);
        $this->db->table('komponen_aset')->insert(['id' => 'donor-part', 'id_aset_series' => 'donor', 'nama_komponen' => 'Motor Fan', 'kondisi' => 'Baik']);
    }

    public function testRecordsAllFourSidesOfOneValidTransfer(): void
    {
        (new KanibalTransfer($this->db))->record($this->data());

        self::assertSame('Tidak Ada', $this->component('donor-part')['kondisi']);
        self::assertCount(1, $this->db->table('riwayat_kanibal')->get()->getResultArray());
        self::assertCount(1, $this->db->table('detail_suku_cadang_lk')->where('sumber', 'Kanibal')->get()->getResultArray());
        $recipient = $this->db->table('komponen_aset')->where('id_aset_series', 'receiver')->get()->getRowArray();
        self::assertSame('Hasil Kanibal', $recipient['asal']);
        self::assertSame('history-1', $recipient['id_riwayat_kanibal']);
    }

    public function testRejectsInvalidDonorWithoutPartialWrites(): void
    {
        $this->db->table('aset_series')->where('id', 'donor')->update(['status' => 'Tersedia']);
        try {
            (new KanibalTransfer($this->db))->record($this->data());
            self::fail('A non-Rusak Berat donor must be rejected.');
        } catch (RuntimeException $e) {
            self::assertStringContainsString('Rusak Berat', $e->getMessage());
        }

        self::assertSame('Baik', $this->component('donor-part')['kondisi']);
        self::assertCount(0, $this->db->table('riwayat_kanibal')->get()->getResultArray());
        self::assertCount(0, $this->db->table('detail_suku_cadang_lk')->get()->getResultArray());
    }

    public function testRejectsLkWhoseReceiverDoesNotMatch(): void
    {
        $data = $this->data();
        $data['id_series_penerima'] = 'donor';
        try {
            (new KanibalTransfer($this->db))->record($data);
            self::fail('A cross-linked LK must be rejected.');
        } catch (RuntimeException $e) {
            self::assertStringContainsString('tidak cocok', $e->getMessage());
        }

        self::assertSame('Baik', $this->component('donor-part')['kondisi']);
        self::assertCount(0, $this->db->table('riwayat_kanibal')->get()->getResultArray());
    }

    private function data(): array
    {
        return [
            'id_lk' => 'lk-1', 'id_series_donor' => 'donor', 'id_series_penerima' => 'receiver',
            'nama_komponen' => 'motor fan', 'kondisi_komponen' => 'Baik',
            'petugas' => 'Admin', 'disetujui_oleh' => 'Admin', 'id_riwayat' => 'history-1',
            'id_detail' => 'detail-1', 'id_komponen_penerima' => 'receiver-part',
        ];
    }

    private function component(string $id): array
    {
        return $this->db->table('komponen_aset')->where('id', $id)->get()->getRowArray();
    }
}
