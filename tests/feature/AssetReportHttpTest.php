<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/** @internal */
final class AssetReportHttpTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['aset_series', 'master_lokasi', 'aset', 'pengguna'] as $table) {
            $this->db->query('DROP TABLE IF EXISTS ' . $this->db->getPrefix() . $table);
        }
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'pengguna (id TEXT PRIMARY KEY, email TEXT, nama_lengkap TEXT, role TEXT, unit TEXT, aktif INTEGER)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'aset (id TEXT PRIMARY KEY, nama TEXT, jenis TEXT, kategori TEXT)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'master_lokasi (id TEXT PRIMARY KEY, gedung TEXT, lantai TEXT, nama_ruangan TEXT, nama_unit TEXT)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'aset_series (id TEXT PRIMARY KEY, id_aset TEXT, id_lokasi TEXT, nomor_aset TEXT, no_seri TEXT, merk TEXT, model TEXT, status TEXT)');
        $this->db->table('pengguna')->insertBatch([
            ['id' => 'admin-1', 'email' => 'admin@test', 'nama_lengkap' => 'Admin', 'role' => 'Admin', 'unit' => 'IPSRS', 'aktif' => 1],
            ['id' => 'tech-1', 'email' => 'tech@test', 'nama_lengkap' => 'Teknisi', 'role' => 'Teknisi', 'unit' => 'IPSRS', 'aktif' => 1],
        ]);
        $this->db->table('aset')->insert(['id' => 'asset-1', 'nama' => 'AC IGD', 'jenis' => 'AC', 'kategori' => 'Non Medis']);
        $this->db->table('master_lokasi')->insert(['id' => 'loc-1', 'gedung' => 'A', 'lantai' => '1', 'nama_ruangan' => 'IGD', 'nama_unit' => 'IGD']);
        $this->db->table('aset_series')->insert(['id' => 'unit-1', 'id_aset' => 'asset-1', 'id_lokasi' => 'loc-1', 'nomor_aset' => 'INV-001', 'no_seri' => 'S-1', 'merk' => 'Daikin', 'model' => 'X1', 'status' => 'Tersedia']);
    }

    public function testAdminCanPrintAssetSnapshotAndDownloadExcel(): void
    {
        $print = $this->withSession(['user_id' => 'admin-1'])->get('ipsrs/laporan/export-print-aset');
        $print->assertOK();
        self::assertStringContainsString('INV-001', (string) $print->response()->getBody());
        self::assertStringContainsString('IGD', (string) $print->response()->getBody());

        $excel = $this->withSession(['user_id' => 'admin-1'])->get('ipsrs/laporan/export-excel-aset');
        $excel->assertOK();
        self::assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $excel->response()->getHeaderLine('Content-Type'));
        self::assertStringStartsWith('PK', (string) $excel->response()->getBody());
    }

    public function testTechnicianCannotAccessAssetReportExports(): void
    {
        $print = $this->withSession(['user_id' => 'tech-1'])->get('ipsrs/laporan/export-print-aset');
        $excel = $this->withSession(['user_id' => 'tech-1'])->get('ipsrs/laporan/export-excel-aset');
        $print->assertRedirectTo('/ipsrs');
        $excel->assertRedirectTo('/ipsrs');
    }
}
