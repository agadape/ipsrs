<?php

use App\Models\LKModel;
use App\Models\StokModel;
use App\Libraries\Metrics;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/** @internal */
final class InventoryTransactionModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $prefix = $this->db->getPrefix();
        foreach (['riwayat_transaksi_stok', 'detail_suku_cadang_lk', 'barang_persediaan'] as $table) {
            $this->db->query('DROP TABLE IF EXISTS ' . $prefix . $table);
        }
        $this->db->query('CREATE TABLE ' . $prefix . 'barang_persediaan (id TEXT PRIMARY KEY, stok_tersedia INTEGER NOT NULL, minimum_stok INTEGER NOT NULL DEFAULT 0)');
        $this->db->query('CREATE TABLE ' . $prefix . 'riwayat_transaksi_stok (id TEXT PRIMARY KEY, id_barang TEXT NOT NULL, nama_barang TEXT, jenis TEXT NOT NULL, jumlah INTEGER NOT NULL, tanggal TEXT, no_dokumen TEXT, keterangan TEXT, petugas TEXT)');
        $this->db->query('CREATE TABLE ' . $prefix . 'detail_suku_cadang_lk (id TEXT PRIMARY KEY, id_lk TEXT NOT NULL, id_barang TEXT, sumber TEXT NOT NULL, nama_barang TEXT, jumlah INTEGER NOT NULL)');
        $this->db->table('barang_persediaan')->insert(['id' => 'barang-1', 'stok_tersedia' => 5, 'minimum_stok' => 5]);
    }

    public function testAtomicDebitWritesBalanceAndLedgerTogether(): void
    {
        $stok = new StokModel();
        $this->useTestConnection($stok);

        $this->db->transBegin();
        $stok->catatTransaksi($this->transaction('Keluar', 2));
        $this->db->transCommit();

        self::assertSame(3, (int) $this->db->table('barang_persediaan')->where('id', 'barang-1')->get()->getRowArray()['stok_tersedia']);
        self::assertCount(1, $this->db->table('riwayat_transaksi_stok')->get()->getResultArray());
    }

    public function testShortageWritesNeitherBalanceNorLedger(): void
    {
        $stok = new StokModel();
        $this->useTestConnection($stok);

        $this->db->transBegin();
        try {
            $stok->catatTransaksi($this->transaction('Keluar', 6));
            self::fail('Shortage must reject the debit.');
        } catch (\RuntimeException $e) {
            self::assertStringContainsString('Stok tidak mencukupi', $e->getMessage());
            $this->db->transRollback();
        }

        self::assertSame(5, (int) $this->db->table('barang_persediaan')->where('id', 'barang-1')->get()->getRowArray()['stok_tersedia']);
        self::assertCount(0, $this->db->table('riwayat_transaksi_stok')->get()->getResultArray());
    }

    public function testBalanceReturnsToLowStockAfterIncomingThenOutgoingMovement(): void
    {
        $this->db->table('barang_persediaan')->where('id', 'barang-1')->update(['stok_tersedia' => 0]);
        $stok = new StokModel();
        $this->useTestConnection($stok);

        $this->db->transBegin();
        $stok->catatTransaksi($this->transaction('Masuk', 10));
        $stok->catatTransaksi($this->transaction('Keluar', 9));
        $this->db->transCommit();

        $barang = $this->db->table('barang_persediaan')->where('id', 'barang-1')->get()->getRowArray();
        self::assertSame(1, (int) $barang['stok_tersedia']);
        self::assertSame('Menipis', Metrics::statusStok((int) $barang['stok_tersedia'], (int) $barang['minimum_stok']));
        self::assertCount(2, $this->db->table('riwayat_transaksi_stok')->get()->getResultArray());
    }

    public function testOnlyGudangPartsAreEligibleForStockRollback(): void
    {
        $this->db->table('detail_suku_cadang_lk')->insertBatch([
            ['id' => 'detail-gudang', 'id_lk' => 'lk-1', 'id_barang' => 'barang-1', 'sumber' => 'Gudang', 'nama_barang' => 'Part', 'jumlah' => 1],
            ['id' => 'detail-kanibal', 'id_lk' => 'lk-1', 'id_barang' => null, 'sumber' => 'Kanibal', 'nama_barang' => 'Donor', 'jumlah' => 1],
        ]);
        $lk = new LKModel();
        $this->useTestConnection($lk);

        self::assertTrue($lk->hasGudangSukuCadang('lk-1', 'barang-1'));
        self::assertCount(1, $lk->getGudangSukuCadang('lk-1'));
    }

    private function transaction(string $jenis, int $jumlah): array
    {
        return [
            'id' => 'ledger-' . $jenis . '-' . $jumlah,
            'id_barang' => 'barang-1',
            'nama_barang' => 'Part',
            'jenis' => $jenis,
            'jumlah' => $jumlah,
            'tanggal' => '2026-09-16',
            'petugas' => 'Admin',
        ];
    }

    private function useTestConnection(object $model): void
    {
        $property = new \ReflectionProperty($model, 'conn');
        $property->setValue($model, $this->db);
    }
}
