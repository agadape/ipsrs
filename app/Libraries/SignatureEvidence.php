<?php

namespace App\Libraries;

/**
 * Validates the image evidence captured by the LK signature canvas.
 *
 * This is deliberately not a cryptographic-signature verifier. It only
 * accepts a bounded PNG data URL so the database stores a real image rather
 * than arbitrary request text.
 */
final class SignatureEvidence
{
    private const PREFIX = 'data:image/png;base64,';
    private const MAX_BYTES = 1_048_576;

    public static function normalize(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || !str_starts_with($value, self::PREFIX)) {
            return null;
        }

        $encoded = substr($value, strlen(self::PREFIX));
        $binary = base64_decode($encoded, true);
        if ($binary === false || $binary === '' || strlen($binary) > self::MAX_BYTES) {
            return null;
        }

        $image = @getimagesizefromstring($binary);
        if ($image === false || ($image['mime'] ?? '') !== 'image/png') {
            return null;
        }

        return self::PREFIX . base64_encode($binary);
    }
}
