<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Laporan publik (/lapor) tidak boleh menghidupkan unit yang sudah dihapuskan,
 * tidak boleh memutus status peminjaman, dan tidak menimpa lokasi master unit.
 *
 * @internal
 */
final class PublicReportLifecycleHttpTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['laporan_kerusakan', 'aset_series', 'master_lokasi', 'aset'] as $table) {
            $this->db->query('DROP TABLE IF EXISTS ' . $this->db->getPrefix() . $table);
        }
        $p = $this->db->getPrefix();
        $this->db->query("CREATE TABLE {$p}aset (id TEXT PRIMARY KEY, nama TEXT, jenis TEXT, kategori TEXT)");
        $this->db->query("CREATE TABLE {$p}master_lokasi (id TEXT PRIMARY KEY, gedung TEXT, lantai TEXT, nama_ruangan TEXT, nama_unit TEXT)");
        $this->db->query("CREATE TABLE {$p}aset_series (id TEXT PRIMARY KEY, id_aset TEXT, id_lokasi TEXT, nomor_aset TEXT, lokasi TEXT, status TEXT)");
        $this->db->query("CREATE TABLE {$p}laporan_kerusakan (id TEXT PRIMARY KEY, no_order TEXT UNIQUE, tanggal TEXT, jam_laporan TEXT, keluhan TEXT, kode TEXT, pelapor TEXT, unit_pelapor TEXT, lokasi TEXT, id_aset_series TEXT, nama_aset TEXT, teknisi TEXT, status TEXT, created_at TEXT)");

        $this->db->table('aset')->insert(['id' => 'aset-1', 'nama' => 'Air Conditioner (AC)']);
        $this->db->table('master_lokasi')->insert(['id' => 'lok-1', 'gedung' => 'A', 'lantai' => '1', 'nama_ruangan' => 'Ruang Uji', 'nama_unit' => 'IGD']);
        foreach (['tersedia' => 'Tersedia', 'dipinjam' => 'Dipinjam', 'dihapus' => 'Dihapuskan'] as $id => $status) {
            $this->db->table('aset_series')->insert([
                'id' => $id, 'id_aset' => 'aset-1', 'id_lokasi' => 'lok-1',
                'nomor_aset' => 'INV-' . $id, 'lokasi' => 'Lokasi Asli', 'status' => $status,
            ]);
        }
    }

    private function lapor(string $seriesId): \CodeIgniter\Test\TestResponse
    {
        return $this->post('lapor', [
            config('Security')->tokenName => service('security')->getHash(),
            'pelapor'        => 'Perawat Jaga',
            'unit_pelapor'   => 'IGD',
            'keluhan'        => 'AC tidak dingin',
            'lokasi'         => 'Teks bebas pelapor',
            'id_aset_series' => $seriesId,
        ]);
    }

    private function series(string $id): array
    {
        return $this->db->table('aset_series')->where('id', $id)->get()->getRowArray();
    }

    public function testAvailableUnitIsMarkedInRepairWithoutOverwritingItsLocation(): void
    {
        $this->lapor('tersedia');

        $this->assertSame(1, $this->db->table('laporan_kerusakan')->countAllResults());
        $this->assertSame('Dalam Perbaikan', $this->series('tersedia')['status']);
        $this->assertSame('Lokasi Asli', $this->series('tersedia')['lokasi']);
    }

    public function testBorrowedUnitKeepsItsLoanStatus(): void
    {
        $this->lapor('dipinjam');

        $this->assertSame(1, $this->db->table('laporan_kerusakan')->countAllResults());
        $this->assertSame('Dipinjam', $this->series('dipinjam')['status']);
    }

    public function testDisposedUnitIsRejectedAndStaysDisposed(): void
    {
        $this->lapor('dihapus');

        $this->assertSame(0, $this->db->table('laporan_kerusakan')->countAllResults());
        $this->assertSame('Dihapuskan', $this->series('dihapus')['status']);
    }

    public function testDisposedUnitIsNotOfferedOnTheForm(): void
    {
        $response = $this->get('lapor');

        $response->assertOK();
        $response->assertSee('INV-tersedia');
        $response->assertDontSee('INV-dihapus');
    }
}
