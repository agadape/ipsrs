<?php

namespace Tests\Unit;

use App\Config\IPSRS;
use CodeIgniter\Test\CIUnitTestCase;

final class LkWorkflowContractTest extends CIUnitTestCase
{
    public function testEveryConfiguredTransitionUsesKnownStatus(): void
    {
        foreach (IPSRS::LK_STATUS_TRANSITIONS as $from => $targets) {
            self::assertContains($from, IPSRS::STATUS_LK);

            foreach ($targets as $to) {
                self::assertContains($to, IPSRS::STATUS_LK);
            }
        }
    }

    public function testClosedLkHasNoForwardTransition(): void
    {
        self::assertSame([], IPSRS::LK_STATUS_TRANSITIONS['Selesai']);
    }

    public function testAssetStatusMappingUsesOnlyDeployedLifecycleValues(): void
    {
        foreach (IPSRS::LK_TO_ASET_STATUS as $status) {
            self::assertContains($status, IPSRS::STATUS_ASET);
        }
    }
}
