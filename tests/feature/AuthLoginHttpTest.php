<?php

use App\Config\IPSRS;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/** @internal */
final class AuthLoginHttpTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $table = $this->db->getPrefix() . 'pengguna';
        $this->db->query('DROP TABLE IF EXISTS ' . $table);
        $this->db->query('CREATE TABLE ' . $table . ' (id TEXT PRIMARY KEY, email TEXT, password_hash TEXT, nama_lengkap TEXT, role TEXT, unit TEXT, aktif INTEGER)');
        $this->db->table('pengguna')->insertBatch([
            [
                'id' => 'active-1', 'email' => 'active@example.test',
                'password_hash' => password_hash('ValidPassword!1', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Admin Aktif', 'role' => 'Admin', 'unit' => 'IPSRS', 'aktif' => 1,
            ],
            [
                'id' => 'inactive-1', 'email' => 'inactive@example.test',
                'password_hash' => password_hash('ValidPassword!1', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Admin Nonaktif', 'role' => 'Admin', 'unit' => 'IPSRS', 'aktif' => 0,
            ],
        ]);

        service('cache')->clean();
    }

    public function testSuccessfulLoginCreatesCanonicalSession(): void
    {
        $response = $this->postLogin('active@example.test', 'ValidPassword!1');

        $response->assertRedirectTo('/ipsrs');
        $response->assertSessionHas('user_id', 'active-1');
        $response->assertSessionHas('user_role', 'admin');
    }

    public function testInactiveAndUnknownAccountsReceiveSameGenericError(): void
    {
        foreach (['inactive@example.test', 'missing@example.test'] as $email) {
            $response = $this->postLogin($email, 'ValidPassword!1');

            $response->assertRedirectTo('/login');
            $response->assertSessionHas('error', 'Email atau kata sandi salah.');
            $response->assertSessionMissing('user_id');
        }
    }

    public function testLoginIsRateLimitedAfterConfiguredAttempts(): void
    {
        $email = 'limited@example.test';

        for ($attempt = 1; $attempt <= IPSRS::LOGIN_MAX_ATTEMPTS; $attempt++) {
            $response = $this->postLogin($email, 'wrong-password');
            $response->assertSessionHas('error', 'Email atau kata sandi salah.');
        }

        $blocked = $this->postLogin($email, 'wrong-password');
        $blocked->assertRedirectTo('/login');
        $blocked->assertSessionHas('error', 'Terlalu banyak percobaan login. Coba lagi beberapa saat.');
        $this->assertNotSame('', $blocked->response()->getHeaderLine('Retry-After'));
    }

    public function testCredentialSprayFromOneIpIsAlsoRateLimited(): void
    {
        for ($attempt = 1; $attempt <= IPSRS::LOGIN_IP_MAX_ATTEMPTS; $attempt++) {
            $this->postLogin('spray-' . $attempt . '@example.test', 'wrong-password');
        }

        $blocked = $this->postLogin('another-account@example.test', 'wrong-password');
        $blocked->assertSessionHas('error', 'Terlalu banyak percobaan login. Coba lagi beberapa saat.');
        $this->assertNotSame('', $blocked->response()->getHeaderLine('Retry-After'));
    }

    public function testSecurityHeadersAreAddedToPublicResponse(): void
    {
        $response = $this->get('login');

        $this->assertSame('SAMEORIGIN', $response->response()->getHeaderLine('X-Frame-Options'));
        $this->assertSame('nosniff', $response->response()->getHeaderLine('X-Content-Type-Options'));
    }

    public function testRegistrationEndpointIsNotRoutable(): void
    {
        $routes = service('routes')->loadRoutes();

        $this->assertArrayNotHasKey('register', $routes->getRoutes('GET'));
        $this->assertArrayNotHasKey('register', $routes->getRoutes('POST'));
    }

    private function postLogin(string $email, string $password): \CodeIgniter\Test\TestResponse
    {
        return $this->post('login', [
            config('Security')->tokenName => service('security')->getHash(),
            'email' => $email,
            'password' => $password,
        ]);
    }
}
