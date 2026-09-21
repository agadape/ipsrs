<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/** @internal */
final class AssetLoanHttpTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    private const SERIES_ID = '33333333-3333-4333-8333-333333333333';

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['peminjaman_aset', 'aset_series', 'master_lokasi', 'aset', 'pengguna'] as $table) {
            $this->db->query('DROP TABLE IF EXISTS ' . $this->db->getPrefix() . $table);
        }
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'pengguna (id TEXT PRIMARY KEY, email TEXT, nama_lengkap TEXT, role TEXT, unit TEXT, aktif INTEGER)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'aset (id TEXT PRIMARY KEY, nama TEXT)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'master_lokasi (id TEXT PRIMARY KEY, gedung TEXT, lantai TEXT, nama_ruangan TEXT, nama_unit TEXT)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'aset_series (id TEXT PRIMARY KEY, id_aset TEXT, id_lokasi TEXT, status TEXT)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'peminjaman_aset (id TEXT PRIMARY KEY, id_aset_series TEXT, nama_peminjam TEXT, unit_peminjam TEXT, tgl_pinjam TEXT, tgl_kembali_rencana TEXT, tgl_kembali_aktual TEXT, status TEXT, keterangan TEXT, id_admin TEXT)');
        $this->db->table('pengguna')->insert(['id' => 'admin-1', 'email' => 'admin@test', 'nama_lengkap' => 'Admin', 'role' => 'Admin', 'unit' => 'IPSRS', 'aktif' => 1]);
        $this->db->table('aset')->insert(['id' => 'aset-1', 'nama' => 'Aset Uji']);
        $this->db->table('master_lokasi')->insert(['id' => 'lokasi-1', 'gedung' => 'A', 'lantai' => '1', 'nama_ruangan' => 'Uji', 'nama_unit' => 'IPSRS']);
        $this->db->table('aset_series')->insert(['id' => self::SERIES_ID, 'id_aset' => 'aset-1', 'id_lokasi' => 'lokasi-1', 'status' => 'Tersedia']);
    }

    public function testAdminCanBorrowAvailableSeriesAtomically(): void
    {
        $response = $this->withSession(['user_id' => 'admin-1'])->post('ipsrs/aset/pinjam', [
            config('Security')->tokenName => service('security')->getHash(),
            'id_aset_series' => self::SERIES_ID,
            'nama_peminjam' => 'Unit Uji',
            'unit_peminjam' => 'IGD',
            'tgl_kembali_rencana' => date('Y-m-d', strtotime('+1 day')),
        ]);

        $response->assertRedirectTo('/ipsrs/aset/series/' . self::SERIES_ID);
        $this->assertSame('Dipinjam', $this->db->table('aset_series')->where('id', self::SERIES_ID)->get()->getRowArray()['status']);
        $this->assertSame('Dipinjam', $this->db->table('peminjaman_aset')->get()->getRowArray()['status']);
    }

    public function testAdminCanReturnExactlyOneActiveLoanAtomically(): void
    {
        $this->db->table('aset_series')->where('id', self::SERIES_ID)->update(['status' => 'Dipinjam']);
        $this->db->table('peminjaman_aset')->insert([
            'id' => 'loan-1', 'id_aset_series' => self::SERIES_ID, 'nama_peminjam' => 'Unit Uji',
            'unit_peminjam' => 'IGD', 'tgl_pinjam' => date('Y-m-d'), 'status' => 'Dipinjam', 'id_admin' => 'admin-1',
        ]);

        $response = $this->withSession(['user_id' => 'admin-1'])->post('ipsrs/aset/kembali/' . self::SERIES_ID, [
            config('Security')->tokenName => service('security')->getHash(),
        ]);

        $response->assertRedirectTo('/ipsrs/aset/series/' . self::SERIES_ID);
        $this->assertSame('Tersedia', $this->db->table('aset_series')->where('id', self::SERIES_ID)->get()->getRowArray()['status']);
        $loan = $this->db->table('peminjaman_aset')->where('id', 'loan-1')->get()->getRowArray();
        $this->assertSame('Selesai', $loan['status']);
        $this->assertNotEmpty($loan['tgl_kembali_aktual']);
    }

    public function testBorrowRejectsMissingRequiredDataWithoutChangingSeries(): void
    {
        $response = $this->withSession(['user_id' => 'admin-1'])->post('ipsrs/aset/pinjam', [
            config('Security')->tokenName => service('security')->getHash(),
            'id_aset_series' => self::SERIES_ID,
        ]);

        $response->assertRedirect();
        $this->assertSame('Tersedia', $this->db->table('aset_series')->where('id', self::SERIES_ID)->get()->getRowArray()['status']);
        $this->assertSame(0, $this->db->table('peminjaman_aset')->countAllResults());
    }

    public function testBorrowRejectsNonAvailableSeriesWithoutCreatingLoan(): void
    {
        $this->db->table('aset_series')->where('id', self::SERIES_ID)->update(['status' => 'Rusak Berat']);

        $response = $this->withSession(['user_id' => 'admin-1'])->post('ipsrs/aset/pinjam', [
            config('Security')->tokenName => service('security')->getHash(),
            'id_aset_series' => self::SERIES_ID,
            'nama_peminjam' => 'Unit Uji',
            'unit_peminjam' => 'IGD',
            'tgl_kembali_rencana' => date('Y-m-d', strtotime('+1 day')),
        ]);

        $response->assertRedirectTo('/ipsrs/aset/series/' . self::SERIES_ID);
        $this->assertSame('Rusak Berat', $this->db->table('aset_series')->where('id', self::SERIES_ID)->get()->getRowArray()['status']);
        $this->assertSame(0, $this->db->table('peminjaman_aset')->countAllResults());
    }

    public function testReturnRollsBackWhenDuplicateActiveLoansExist(): void
    {
        $this->db->table('aset_series')->where('id', self::SERIES_ID)->update(['status' => 'Dipinjam']);
        $this->db->table('peminjaman_aset')->insertBatch([
            ['id' => 'loan-1', 'id_aset_series' => self::SERIES_ID, 'nama_peminjam' => 'A', 'unit_peminjam' => 'IGD', 'tgl_pinjam' => date('Y-m-d'), 'status' => 'Dipinjam', 'id_admin' => 'admin-1'],
            ['id' => 'loan-2', 'id_aset_series' => self::SERIES_ID, 'nama_peminjam' => 'B', 'unit_peminjam' => 'ICU', 'tgl_pinjam' => date('Y-m-d'), 'status' => 'Dipinjam', 'id_admin' => 'admin-1'],
        ]);

        $response = $this->withSession(['user_id' => 'admin-1'])->post('ipsrs/aset/kembali/' . self::SERIES_ID, [
            config('Security')->tokenName => service('security')->getHash(),
        ]);

        $response->assertRedirectTo('/ipsrs/aset/series/' . self::SERIES_ID);
        $this->assertSame('Dipinjam', $this->db->table('aset_series')->where('id', self::SERIES_ID)->get()->getRowArray()['status']);
        $this->assertSame(2, $this->db->table('peminjaman_aset')->where('status', 'Dipinjam')->countAllResults());
        $this->assertSame(0, $this->db->table('peminjaman_aset')->where('status', 'Selesai')->countAllResults());
    }
}
