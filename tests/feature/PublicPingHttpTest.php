<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/** @internal */
final class PublicPingHttpTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    private const SERIES_ID = '11111111-1111-4111-8111-111111111111';

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['aset_series', 'master_lokasi', 'aset'] as $table) {
            $this->db->query('DROP TABLE IF EXISTS ' . $this->db->getPrefix() . $table);
        }
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'aset (id TEXT PRIMARY KEY, nama TEXT)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'master_lokasi (id TEXT PRIMARY KEY, gedung TEXT, lantai TEXT, nama_ruangan TEXT, nama_unit TEXT)');
        $this->db->query('CREATE TABLE ' . $this->db->getPrefix() . 'aset_series (id TEXT PRIMARY KEY, id_aset TEXT, id_lokasi TEXT, status TEXT, last_seen_at TEXT, last_seen_lat REAL, last_seen_lng REAL, last_seen_by TEXT)');
        $this->db->table('aset')->insert(['id' => 'aset-1', 'nama' => 'Pompa Uji']);
        $this->db->table('master_lokasi')->insert(['id' => 'lokasi-1', 'gedung' => 'A', 'lantai' => '1', 'nama_ruangan' => 'Ruang Uji', 'nama_unit' => 'IPSRS']);
        $this->db->table('aset_series')->insert([
            'id' => self::SERIES_ID, 'id_aset' => 'aset-1', 'id_lokasi' => 'lokasi-1', 'status' => 'Tersedia',
            'last_seen_at' => null, 'last_seen_lat' => null, 'last_seen_lng' => null, 'last_seen_by' => null,
        ]);
    }

    public function testPublicPingWithValidCsrfStoresTelemetryAndSessionActor(): void
    {
        $response = $this
            ->withSession(['user_name' => 'Petugas Scan'])
            ->withHeaders(['X-CSRF-TOKEN' => service('security')->getHash()])
            ->withBodyFormat('json')
            ->post('ipsrs/aset/' . self::SERIES_ID . '/ping', ['lat' => -7.801, 'lng' => 110.364]);

        $response->assertOK();
        $response->assertJSONFragment(['ok' => true, 'updated' => true]);
        $series = $this->db->table('aset_series')->where('id', self::SERIES_ID)->get()->getRowArray();
        $this->assertSame(-7.801, (float) $series['last_seen_lat']);
        $this->assertSame(110.364, (float) $series['last_seen_lng']);
        $this->assertSame('Petugas Scan', $series['last_seen_by']);
        $this->assertNotEmpty($series['last_seen_at']);
    }

    public function testPublicPingRejectsOutOfRangeCoordinatesWithoutWritingTelemetry(): void
    {
        $response = $this
            ->withHeaders(['X-CSRF-TOKEN' => service('security')->getHash()])
            ->withBodyFormat('json')
            ->post('ipsrs/aset/' . self::SERIES_ID . '/ping', ['lat' => 91, 'lng' => 181]);

        $response->assertStatus(400);
        $series = $this->db->table('aset_series')->where('id', self::SERIES_ID)->get()->getRowArray();
        $this->assertNull($series['last_seen_at']);
        $this->assertNull($series['last_seen_lat']);
        $this->assertNull($series['last_seen_lng']);
    }

    public function testPublicPingReturnsNotFoundForUnknownSeries(): void
    {
        $response = $this
            ->withHeaders(['X-CSRF-TOKEN' => service('security')->getHash()])
            ->withBodyFormat('json')
            ->post('ipsrs/aset/22222222-2222-4222-8222-222222222222/ping', ['lat' => -7.8, 'lng' => 110.3]);

        $response->assertStatus(404);
    }

    public function testPublicPingRateLimitDoesNotOverwriteRecentTelemetry(): void
    {
        $this->db->table('aset_series')->where('id', self::SERIES_ID)->update([
            'last_seen_at' => date('Y-m-d H:i:s'),
            'last_seen_lat' => -7.7,
            'last_seen_lng' => 110.2,
            'last_seen_by' => 'Pemindai Pertama',
        ]);

        $response = $this
            ->withHeaders(['X-CSRF-TOKEN' => service('security')->getHash()])
            ->withBodyFormat('json')
            ->post('ipsrs/aset/' . self::SERIES_ID . '/ping', ['lat' => -7.9, 'lng' => 110.5]);

        $response->assertOK();
        $response->assertJSONFragment(['ok' => true, 'updated' => false]);
        $series = $this->db->table('aset_series')->where('id', self::SERIES_ID)->get()->getRowArray();
        $this->assertSame(-7.7, (float) $series['last_seen_lat']);
        $this->assertSame(110.2, (float) $series['last_seen_lng']);
        $this->assertSame('Pemindai Pertama', $series['last_seen_by']);
    }
}
