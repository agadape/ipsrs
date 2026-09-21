<?php

use CodeIgniter\Security\Exceptions\SecurityException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/** @internal */
final class AuthFilterHttpTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $table = $this->db->getPrefix() . 'pengguna';
        $this->db->query('DROP TABLE IF EXISTS ' . $table);
        $this->db->query('DROP TABLE IF EXISTS ' . $this->db->getPrefix() . 'laporan_kerusakan');
        $this->db->query('DROP TABLE IF EXISTS ' . $this->db->getPrefix() . 'jadwal_preventif');
        $this->db->query('CREATE TABLE ' . $table . ' (id TEXT PRIMARY KEY, email TEXT, password_hash TEXT, nama_lengkap TEXT, role TEXT, unit TEXT, aktif INTEGER)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'laporan_kerusakan (id TEXT PRIMARY KEY, status TEXT, teknisi TEXT, pelapor TEXT, keluhan TEXT)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'jadwal_preventif (id TEXT PRIMARY KEY, teknisi TEXT, status TEXT)');
        $this->db->table('pengguna')->insertBatch([
            ['id' => 'admin-1', 'email' => 'admin@example.test', 'nama_lengkap' => 'Admin', 'role' => 'Admin', 'unit' => 'IPSRS', 'aktif' => 1],
            ['id' => 'tech-1', 'email' => 'tech@example.test', 'nama_lengkap' => 'Teknisi', 'role' => 'Teknisi', 'unit' => 'IPSRS', 'aktif' => 1],
            ['id' => 'reporter-1', 'email' => 'reporter@example.test', 'nama_lengkap' => 'Pelapor', 'role' => 'Pelapor', 'unit' => 'IGD', 'aktif' => 1],
            ['id' => 'inactive-1', 'email' => 'inactive@example.test', 'nama_lengkap' => 'Nonaktif', 'role' => 'Teknisi', 'unit' => 'IPSRS', 'aktif' => 0],
        ]);
        $this->db->table('laporan_kerusakan')->insert(['id' => 'lk-other', 'status' => 'Survei', 'teknisi' => 'Teknisi Lain', 'pelapor' => 'Pelapor', 'keluhan' => 'Keluhan awal']);
        $this->db->table('laporan_kerusakan')->insert(['id' => 'lk-open', 'status' => 'Laporan Masuk', 'teknisi' => null, 'pelapor' => 'Pelapor', 'keluhan' => 'Keluhan terbuka']);
        $this->db->table('laporan_kerusakan')->insert(['id' => 'lk-finished', 'status' => 'Selesai', 'teknisi' => 'Teknisi', 'pelapor' => 'Pelapor', 'keluhan' => 'Keluhan selesai']);
        $this->db->table('jadwal_preventif')->insert(['id' => 'jadwal-other', 'teknisi' => 'Teknisi Lain', 'status' => 'Belum']);
    }

    public function testTeknisiCannotOpenKanibalModule(): void
    {
        $response = $this->withSession(['user_id' => 'tech-1'])->get('ipsrs/kanibal');

        $response->assertRedirectTo('/ipsrs');
    }

    public function testPelaporCannotOpenAssetModule(): void
    {
        $response = $this->withSession(['user_id' => 'reporter-1'])->get('ipsrs/aset');

        $response->assertRedirectTo('/ipsrs');
    }

    public function testInactiveSessionIsRedirectedToLogin(): void
    {
        $response = $this->withSession(['user_id' => 'inactive-1'])->get('ipsrs/lk');

        $response->assertRedirectTo('/login');
    }

    public function testAnonymousCannotOpenUuidShapedAssetDetailRoute(): void
    {
        $response = $this->get('ipsrs/aset/11111111-1111-4111-8111-111111111111');

        $response->assertRedirectTo('/login');
    }

    public function testTechnicianCannotReadAnotherTechniciansLkById(): void
    {
        $response = $this->withSession(['user_id' => 'tech-1'])->get('ipsrs/lk/lk-other');

        $response->assertRedirectTo('/ipsrs/lk');
    }

    public function testTechnicianCannotOpenAnotherTechniciansPreventiveLkp(): void
    {
        $response = $this->withSession(['user_id' => 'tech-1'])->get('ipsrs/preventif/lkp/jadwal-other');

        $response->assertRedirectTo('/ipsrs/preventif');
    }

    public function testTechnicianCannotReadAnotherTechniciansPreventiveLkpResult(): void
    {
        $response = $this->withSession(['user_id' => 'tech-1'])->get('ipsrs/preventif/lkp-hasil/jadwal-other');

        $response->assertRedirectTo('/ipsrs/preventif');
    }

    public function testTechnicianCannotSubmitAnotherTechniciansPreventiveLkpEvenWithValidCsrfToken(): void
    {
        $response = $this->withSession(['user_id' => 'tech-1'])->post('ipsrs/preventif/lkp/jadwal-other', [
            config('Security')->tokenName => service('security')->getHash(),
            'kategori' => 'Listrik',
            'hasil_pemeriksaan' => 'Baik',
            'lokasi_sesuai' => 'Sesuai',
            'nama_user_ttd' => 'Penguji',
            'items' => [],
        ]);

        $response->assertRedirectTo('/ipsrs/preventif');
        $this->assertSame('Belum', $this->db->table('jadwal_preventif')->where('id', 'jadwal-other')->get()->getRowArray()['status']);
    }

    public function testPelaporCannotClaimLkEvenWithValidCsrfToken(): void
    {
        $response = $this->withSession(['user_id' => 'reporter-1'])->post('ipsrs/lk/claim/lk-open', [
            config('Security')->tokenName => service('security')->getHash(),
        ]);

        $response->assertRedirectTo('/ipsrs/lk');
        $this->assertSame('Laporan Masuk', $this->db->table('laporan_kerusakan')->where('id', 'lk-open')->get()->getRowArray()['status']);
        $this->assertNull($this->db->table('laporan_kerusakan')->where('id', 'lk-open')->get()->getRowArray()['teknisi']);
    }

    public function testTechnicianCannotDeletePreventiveScheduleEvenWithValidCsrfToken(): void
    {
        $response = $this->withSession(['user_id' => 'tech-1'])->post('ipsrs/preventif/jadwal-other/hapus', [
            config('Security')->tokenName => service('security')->getHash(),
        ]);

        $response->assertRedirectTo('/ipsrs');
        $this->assertSame('jadwal-other', $this->db->table('jadwal_preventif')->where('id', 'jadwal-other')->get()->getRowArray()['id']);
    }

    public function testTechnicianCannotDownloadBaDocumentByDirectUrl(): void
    {
        $response = $this->withSession(['user_id' => 'tech-1'])->get('ipsrs/aset/penghapusan/ba-other/ba');

        $response->assertRedirectTo('/ipsrs');
    }

    public function testTechnicianCannotMutateAnotherTechniciansLkEvenWithValidCsrfToken(): void
    {
        $requests = [
            ['ipsrs/lk/lk-other/detail', ['keluhan' => 'Manipulasi detail']],
            ['ipsrs/lk/lk-other/suku-cadang', ['id_barang' => 'barang-palsu', 'jumlah' => 1]],
            ['ipsrs/lk/lk-other/vendor', ['id_vendor' => 'vendor-palsu']],
        ];

        foreach ($requests as [$path, $payload]) {
            $response = $this->withSession(['user_id' => 'tech-1'])->post($path, [
                config('Security')->tokenName => service('security')->getHash(),
                ...$payload,
            ]);

            $response->assertRedirectTo('/ipsrs/lk');
        }

        $lk = $this->db->table('laporan_kerusakan')->where('id', 'lk-other')->get()->getRowArray();
        $this->assertSame('Keluhan awal', $lk['keluhan']);
        $this->assertSame('Teknisi Lain', $lk['teknisi']);
    }

    public function testPelaporCannotDeleteLkEvenWithValidCsrfToken(): void
    {
        $response = $this->withSession(['user_id' => 'reporter-1'])->post('ipsrs/lk/lk-other/delete', [
            config('Security')->tokenName => service('security')->getHash(),
        ]);

        $response->assertRedirectTo('/ipsrs/lk');
        $this->assertSame('lk-other', $this->db->table('laporan_kerusakan')->where('id', 'lk-other')->get()->getRowArray()['id']);
    }

    public function testFinishedLkRejectsFurtherMutationsEvenWithValidCsrfToken(): void
    {
        $requests = [
            ['ipsrs/lk/lk-finished/detail', ['keluhan' => 'Diubah setelah selesai']],
            ['ipsrs/lk/lk-finished/suku-cadang', ['id_barang' => 'barang-palsu', 'jumlah' => 1]],
            ['ipsrs/lk/lk-finished/vendor', ['id_vendor' => 'vendor-palsu']],
            ['ipsrs/lk/lk-finished/status', ['status_baru' => 'Survei']],
        ];

        foreach ($requests as [$path, $payload]) {
            $response = $this->withSession(['user_id' => 'tech-1'])->post($path, [
                config('Security')->tokenName => service('security')->getHash(),
                ...$payload,
            ]);
            $response->assertRedirectTo('/ipsrs/lk/lk-finished');
        }

        $lk = $this->db->table('laporan_kerusakan')->where('id', 'lk-finished')->get()->getRowArray();
        $this->assertSame('Selesai', $lk['status']);
        $this->assertSame('Keluhan selesai', $lk['keluhan']);
    }

    public function testPublicReportPostWithoutCsrfTokenIsRejectedBeforeController(): void
    {
        $this->expectException(SecurityException::class);

        $this->post('lapor', [
            'pelapor' => 'Penguji', 'unit_pelapor' => 'IGD',
            'keluhan' => 'Uji CSRF', 'lokasi' => 'IGD',
        ]);
    }
}
