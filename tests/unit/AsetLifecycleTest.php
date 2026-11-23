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

    public function testBorrowedUnitKeepsLoanStateWhenLkChanges(): void
    {
        self::assertFalse(AsetLifecycle::canSyncFromLk('Dipinjam'));
        self::assertTrue(AsetLifecycle::canSyncFromLk('Tersedia'));
        self::assertTrue(AsetLifecycle::canSyncFromLk('Dalam Perbaikan'));
    }

    public function testUnitStatusFollowsOpenLkButNeverOverridesProtectedStates(): void
    {
        self::assertSame('Dalam Perbaikan', AsetLifecycle::statusFromOpenLk('Tersedia', true));
        self::assertSame('Tersedia', AsetLifecycle::statusFromOpenLk('Dalam Perbaikan', false));
        self::assertSame('Dalam Perbaikan', AsetLifecycle::statusFromOpenLk('Dalam Perbaikan', true));
        foreach (['Dipinjam', 'Rusak Berat', 'Dihapuskan'] as $status) {
            self::assertNull(AsetLifecycle::statusFromOpenLk($status, true));
            self::assertNull(AsetLifecycle::statusFromOpenLk($status, false));
        }
    }

    public function testOnlyDisposedUnitsCannotReceiveNewLk(): void
    {
        self::assertFalse(AsetLifecycle::canReceiveLk('Dihapuskan'));
        foreach (['Tersedia', 'Dipinjam', 'Dalam Perbaikan', 'Rusak Berat'] as $status) {
            self::assertTrue(AsetLifecycle::canReceiveLk($status));
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
