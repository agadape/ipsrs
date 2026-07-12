<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\AsetModel;
use App\Models\LkpModel;

/**
 * @internal
 */
final class BaseModelTest extends CIUnitTestCase
{
    // ── extractRow ────────────────────────────────────────────────

    public function testExtractRowWithNumericArray(): void
    {
        $model = new AsetModel();
        $ref   = new ReflectionMethod($model, 'extractRow');

        $result = $ref->invoke($model, [['id' => 'A-00001', 'nama' => 'AC']]);
        $this->assertSame('A-00001', $result['id']);
        $this->assertSame('AC', $result['nama']);
    }

    public function testExtractRowWithAssocArray(): void
    {
        $model = new AsetModel();
        $ref   = new ReflectionMethod($model, 'extractRow');

        $result = $ref->invoke($model, ['id' => 'A-00001', 'nama' => 'AC']);
        $this->assertSame('A-00001', $result['id']);
    }

    public function testExtractRowWithNull(): void
    {
        $model = new AsetModel();
        $ref   = new ReflectionMethod($model, 'extractRow');

        $result = $ref->invoke($model, null);
        $this->assertSame([], $result);
    }

    public function testExtractRowWithEmptyArray(): void
    {
        $model = new AsetModel();
        $ref   = new ReflectionMethod($model, 'extractRow');

        $result = $ref->invoke($model, []);
        $this->assertSame([], $result);
    }

    // ── nextNoOrder ───────────────────────────────────────────────

    public function testNextNoOrderLkpFormat(): void
    {
        $model = new LkpModel();
        $ref   = new ReflectionMethod($model, 'nextNoOrder');

        $result = $ref->invoke($model, 'no_order', 'LKP-' . date('Ym') . '-', 4);
        $this->assertMatchesRegularExpression('/^LKP-\d{6}-\d{4}$/', $result);
    }
}
