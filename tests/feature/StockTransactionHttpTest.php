<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Libraries\Metrics;

/** @internal */
final class StockTransactionHttpTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $prefix = $this->db->getPrefix();
        foreach (['riwayat_transaksi_stok', 'barang_persediaan', 'pengguna'] as $table) {
            $this->db->query('DROP TABLE IF EXISTS ' . $prefix . $table);
        }

        $this->db->query('CREATE TABLE ' . $prefix . 'pengguna (id TEXT PRIMARY KEY, email TEXT, nama_lengkap TEXT, role TEXT, unit TEXT, aktif INTEGER)');
        $this->db->query('CREATE TABLE ' . $prefix . 'barang_persediaan (id TEXT PRIMARY KEY, nama TEXT, stok_tersedia INTEGER NOT NULL, minimum_stok INTEGER NOT NULL)');
        $this->db->query('CREATE TABLE ' . $prefix . 'riwayat_transaksi_stok (id TEXT PRIMARY KEY, id_barang TEXT NOT NULL, nama_barang TEXT, jenis TEXT NOT NULL, jumlah INTEGER NOT NULL, tanggal TEXT, no_dokumen TEXT, keterangan TEXT, petugas TEXT)');
        $this->db->table('pengguna')->insert(['id' => 'admin-1', 'email' => 'admin@test', 'nama_lengkap' => 'Admin', 'role' => 'Admin', 'unit' => 'IPSRS', 'aktif' => 1]);
        $this->db->table('barang_persediaan')->insert(['id' => 'barang-1', 'nama' => 'Freon', 'stok_tersedia' => 0, 'minimum_stok' => 5]);
    }

    public function testIncomingStockRejectsMissingDocumentNumberWithoutMutation(): void
    {
        $response = $this->withSession(['user_id' => 'admin-1'])->post('ipsrs/stok/masuk', [
            config('Security')->tokenName => service('security')->getHash(),
            'id_barang' => 'barang-1',
            'jumlah' => 10,
            'tanggal' => '2026-09-22',
            'no_dokumen' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        self::assertSame(0, (int) $this->db->table('barang_persediaan')->where('id', 'barang-1')->get()->getRowArray()['stok_tersedia']);
        self::assertSame(0, $this->db->table('riwayat_transaksi_stok')->countAllResults());
    }

    public function testIncomingThenOutgoingStockReachesLowStockWithDocumentLedger(): void
    {
        $incoming = $this->withSession(['user_id' => 'admin-1'])->post('ipsrs/stok/masuk', [
            config('Security')->tokenName => service('security')->getHash(),
            'id_barang' => 'barang-1',
            'jumlah' => 10,
            'tanggal' => '2026-09-22',
            'no_dokumen' => 'INV-2026-001',
        ]);
        $incoming->assertRedirectTo('/ipsrs/stok');

        $outgoing = $this->withSession(['user_id' => 'admin-1'])->post('ipsrs/stok/keluar', [
            config('Security')->tokenName => service('security')->getHash(),
            'id_barang' => 'barang-1',
            'jumlah' => 9,
            'tanggal' => '2026-09-22',
            'no_dokumen' => '',
        ]);
        $outgoing->assertRedirectTo('/ipsrs/stok');

        $barang = $this->db->table('barang_persediaan')->where('id', 'barang-1')->get()->getRowArray();
        self::assertSame(1, (int) $barang['stok_tersedia']);
        self::assertSame('Menipis', Metrics::statusStok((int) $barang['stok_tersedia'], (int) $barang['minimum_stok']));
        $ledger = $this->db->table('riwayat_transaksi_stok')->orderBy('jenis')->get()->getResultArray();
        self::assertCount(2, $ledger);
        self::assertSame('INV-2026-001', $this->db->table('riwayat_transaksi_stok')->where('jenis', 'Masuk')->get()->getRowArray()['no_dokumen']);
    }
}
