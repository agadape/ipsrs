<?php

use App\Libraries\LkpChecklist;
use App\Models\JadwalModel;
use App\Models\LkpModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/** @internal */
final class PreventifCompletionModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $prefix = $this->db->getPrefix();
        $this->db->query('DROP TABLE IF EXISTS ' . $prefix . 'detail_checklist_lkp');
        $this->db->query('DROP TABLE IF EXISTS ' . $prefix . 'lembar_kerja_preventif');
        $this->db->query('DROP TABLE IF EXISTS ' . $prefix . 'jadwal_preventif');
        $this->db->query('CREATE TABLE ' . $prefix . 'jadwal_preventif (id TEXT PRIMARY KEY, status TEXT NOT NULL)');
        $this->db->query('CREATE TABLE ' . $prefix . 'lembar_kerja_preventif (id TEXT PRIMARY KEY, no_order TEXT NOT NULL, id_jadwal TEXT, created_at TEXT)');
        $this->db->query('CREATE TABLE ' . $prefix . 'detail_checklist_lkp (id TEXT PRIMARY KEY, id_lkp TEXT NOT NULL, no_item INTEGER, jenis_item TEXT, nama_komponen TEXT, hasil_inspeksi TEXT, hasil_service TEXT, nilai_pengukuran TEXT, satuan TEXT, keterangan TEXT)');
    }

    public function testOneScheduleCanCreateOnlyOneLkpCompletion(): void
    {
        $this->db->table('jadwal_preventif')->insert(['id' => 'jadwal-1', 'status' => 'Belum']);
        $jadwal = new JadwalModel();
        $lkp = new LkpModel();
        $this->useTestConnection($jadwal);
        $this->useTestConnection($lkp);

        $this->db->transBegin();
        self::assertTrue($jadwal->claimForCompletion('jadwal-1'));

        $header = $lkp->create([
            'id' => 'lkp-1',
            'no_order' => 'LKP-202609-0001',
            'id_jadwal' => 'jadwal-1',
            'created_at' => '2026-09-16 10:00:00',
        ]);
        $lkp->addDetail(LkpChecklist::toRows('lkp-1', [[
            'jenis' => 'Teks',
            'komponen' => 'Kesesuaian Lokasi Aset',
            'hasil' => 'Sesuai',
            'ket' => '',
        ]], static fn(): string => 'detail-1'));
        $jadwal->markSelesai('jadwal-1');
        $this->db->transCommit();

        self::assertSame('lkp-1', $header['id']);
        self::assertSame('Selesai', $this->db->table('jadwal_preventif')->where('id', 'jadwal-1')->get()->getRowArray()['status']);
        self::assertCount(1, $this->db->table('lembar_kerja_preventif')->get()->getResultArray());
        self::assertSame('Sesuai', LkpChecklist::textPayload($this->db->table('detail_checklist_lkp')->get()->getRowArray()['keterangan'])['hasil']);
        self::assertFalse($jadwal->claimForCompletion('jadwal-1'));
    }

    private function useTestConnection(object $model): void
    {
        $property = new \ReflectionProperty($model, 'conn');
        $property->setValue($model, $this->db);
    }
}
