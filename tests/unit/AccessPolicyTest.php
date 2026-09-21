<?php

namespace Tests\Unit;

use App\Libraries\AccessPolicy;
use CodeIgniter\Test\CIUnitTestCase;

final class AccessPolicyTest extends CIUnitTestCase
{
    private array $incoming = ['status' => 'Laporan Masuk', 'pelapor' => 'Rani', 'teknisi' => ''];
    private array $assigned = ['status' => 'Survei', 'pelapor' => 'Rani', 'teknisi' => 'Tono'];

    public function testLkVisibilityIsBoundToRoleAndLegacyNameScope(): void
    {
        self::assertTrue(AccessPolicy::canViewLk($this->assigned, 'Admin', 'Any'));
        self::assertTrue(AccessPolicy::canViewLk($this->assigned, 'Pelapor', 'Rani'));
        self::assertFalse(AccessPolicy::canViewLk($this->assigned, 'Pelapor', 'Other'));
        self::assertTrue(AccessPolicy::canViewLk($this->incoming, 'Teknisi', 'Tono'));
        self::assertFalse(AccessPolicy::canViewLk($this->assigned, 'Teknisi', 'Other'));
    }

    public function testOnlyAssignedTechnicianOrAdminManagesLk(): void
    {
        self::assertTrue(AccessPolicy::canManageLk($this->assigned, 'teknisi', 'Tono'));
        self::assertFalse(AccessPolicy::canManageLk($this->assigned, 'teknisi', 'Other'));
        self::assertFalse(AccessPolicy::canManageLk($this->assigned, 'pelapor', 'Rani'));
        self::assertTrue(AccessPolicy::canManageLk($this->assigned, 'admin', 'Other'));
    }

    public function testPreventiveAccessIsBoundToAssignedTechnician(): void
    {
        $jadwal = ['teknisi' => 'Tono'];

        self::assertTrue(AccessPolicy::canAccessPreventive($jadwal, 'teknisi', 'Tono'));
        self::assertFalse(AccessPolicy::canAccessPreventive($jadwal, 'teknisi', 'Other'));
        self::assertFalse(AccessPolicy::canAccessPreventive($jadwal, 'pelapor', 'Tono'));
        self::assertTrue(AccessPolicy::canAccessPreventive($jadwal, 'admin', 'Other'));
    }
}
