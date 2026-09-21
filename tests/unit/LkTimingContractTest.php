<?php

namespace Tests\Unit;

use App\Controllers\LK;
use CodeIgniter\Test\CIUnitTestCase;
use ReflectionMethod;

final class LkTimingContractTest extends CIUnitTestCase
{
    public function testExistingZeroResponseTimeIsNotOverwritten(): void
    {
        $controller = new LK();
        $method = new ReflectionMethod($controller, 'calcResponseTime');
        $lk = ['tanggal' => '2026-09-16', 'jam_laporan' => '09:00', 'response_time' => 0];
        $data = ['tanggal_cek' => '2026-09-16', 'jam_cek' => '09:10'];

        $method->invokeArgs($controller, [$lk, 'Survei', &$data]);

        self::assertArrayNotHasKey('response_time', $data);
    }

    public function testFirstSurveyPersistsResponseTime(): void
    {
        $controller = new LK();
        $method = new ReflectionMethod($controller, 'calcResponseTime');
        $lk = ['tanggal' => '2026-09-16', 'jam_laporan' => '09:00', 'response_time' => null];
        $data = ['tanggal_cek' => '2026-09-16', 'jam_cek' => '09:05'];

        $method->invokeArgs($controller, [$lk, 'Survei', &$data]);

        self::assertSame(5, $data['response_time']);
    }

    public function testCompletionDoesNotSubstituteDownTimeForMissingResponseTime(): void
    {
        $controller = new LK();
        $method = new ReflectionMethod($controller, 'calcDownTime');
        $lk = ['tanggal' => date('Y-m-d'), 'jam_laporan' => '00:00', 'response_time' => null];
        $data = ['tanggal_selesai' => '2000-01-01', 'jam_selesai' => '00:00'];

        $method->invokeArgs($controller, [$lk, 'Selesai', &$data]);

        self::assertArrayHasKey('down_time', $data);
        self::assertArrayNotHasKey('response_time', $data);
        self::assertSame(date('Y-m-d'), $data['tanggal_selesai']);
        self::assertSame(date('H:i'), $data['jam_selesai']);
    }

    public function testFutureReportCannotBeCompleted(): void
    {
        $controller = new LK();
        $method = new ReflectionMethod($controller, 'validateTransitionTiming');
        $lk = ['tanggal' => '2999-01-01', 'jam_laporan' => '00:00'];

        $error = $method->invoke($controller, $lk, 'Selesai', []);

        self::assertNotNull($error);
    }
}
