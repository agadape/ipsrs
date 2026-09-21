<?php

namespace Tests\Unit;

use App\Libraries\SignatureEvidence;
use CodeIgniter\Test\CIUnitTestCase;

final class SignatureEvidenceTest extends CIUnitTestCase
{
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScL9YQAAAABJRU5ErkJggg==';

    public function testAcceptsAndNormalizesValidPngDataUrl(): void
    {
        $actual = SignatureEvidence::normalize('data:image/png;base64,' . self::PNG);

        self::assertSame('data:image/png;base64,' . self::PNG, $actual);
    }

    public function testRejectsNonPngAndMalformedPayloads(): void
    {
        self::assertNull(SignatureEvidence::normalize('data:text/plain;base64,SGVsbG8='));
        self::assertNull(SignatureEvidence::normalize('data:image/png;base64,not-base64'));
        self::assertNull(SignatureEvidence::normalize('data:image/png;base64,SGVsbG8='));
    }
}
