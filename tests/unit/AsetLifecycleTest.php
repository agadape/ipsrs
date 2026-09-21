<?php

namespace Tests\Unit;

use App\Libraries\AsetLifecycle;
use CodeIgniter\Test\CIUnitTestCase;

final class AsetLifecycleTest extends CIUnitTestCase
{
    public function testBorrowAndReturnRequireExactLifecycleState(): void
    {
        self::assertTrue(AsetLifecycle::canBorrow('Tersedia'));
        self::assertFalse(AsetLifecycle::canBorrow('Dihapuskan'));
        self::assertTrue(AsetLifecycle::canReturn('Dipinjam'));
        self::assertFalse(AsetLifecycle::canReturn('Rusak Berat'));
    }

    public function testProtectedStatesCannotBeRevivedOrRelocatedAutomatically(): void
    {
        foreach (['Dihapuskan', 'Rusak Berat'] as $status) {
            self::assertFalse(AsetLifecycle::canSyncFromLk($status));
            self::assertFalse(AsetLifecycle::canRelocate($status));
        }
    }

    public function testDisposalRequiresRusakBeratAndLegacyValuesAreNotCanonical(): void
    {
        self::assertTrue(AsetLifecycle::canDispose('Rusak Berat'));
        self::assertFalse(AsetLifecycle::canDispose('Tersedia'));
        self::assertFalse(AsetLifecycle::isCanonical('Kanibal'));
        self::assertFalse(AsetLifecycle::isCanonical('Dibuang'));
    }
}
