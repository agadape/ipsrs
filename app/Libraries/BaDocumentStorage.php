<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

final class BaDocumentStorage
{
    public const MAX_SIZE_BYTES = 5 * 1024 * 1024;

    /** @var array<string, string> */
    private const MIME_EXTENSIONS = [
        'application/pdf' => 'pdf',
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
    ];

    public function validate(UploadedFile $file): ?string
    {
        if (! $file->isValid() || $file->hasMoved()) {
            return 'Upload dokumen BA gagal. Silakan pilih file kembali.';
        }

        return $this->validateDetectedFile($file->getMimeType(), $file->getSize());
    }

    public function validateDetectedFile(string $mimeType, int $size): ?string
    {
        if ($size > self::MAX_SIZE_BYTES) {
            return 'Dokumen BA maksimal berukuran 5 MB.';
        }

        if (! isset(self::MIME_EXTENSIONS[$mimeType])) {
            return 'Dokumen BA hanya menerima PDF, JPG, atau PNG yang valid.';
        }

        return null;
    }

    public function store(UploadedFile $file): string
    {
        $error = $this->validate($file);
        if ($error !== null) {
            throw new RuntimeException($error);
        }

        $extension = self::MIME_EXTENSIONS[$file->getMimeType()];
        $storageKey = bin2hex(random_bytes(16)) . '.' . $extension;

        try {
            $file->move($this->storageDirectory(), $storageKey);
        } catch (\Throwable $exception) {
            throw new RuntimeException('Dokumen BA tidak dapat disimpan.', 0, $exception);
        }

        return $storageKey;
    }

    public function resolve(string $storageKey): ?string
    {
        $safeName = basename($storageKey);
        if ($safeName !== $storageKey || $safeName === '') {
            return null;
        }

        $storedPath = $this->storageDirectory() . DIRECTORY_SEPARATOR . $safeName;
        if (is_file($storedPath)) {
            return $storedPath;
        }

        // Existing BA files remain readable only through the protected controller.
        $legacyPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ba' . DIRECTORY_SEPARATOR . $safeName;

        return is_file($legacyPath) ? $legacyPath : null;
    }

    public function delete(string $storageKey): void
    {
        $safeName = basename($storageKey);
        if ($safeName !== $storageKey || ! preg_match('/^[a-f0-9]{32}\.(pdf|jpg|png)$/', $safeName)) {
            return;
        }

        $path = $this->storageDirectory() . DIRECTORY_SEPARATOR . $safeName;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    public function downloadExtension(string $storageKey): string
    {
        $extension = strtolower(pathinfo(basename($storageKey), PATHINFO_EXTENSION));

        return in_array($extension, ['pdf', 'jpg', 'png'], true) ? $extension : 'bin';
    }

    private function storageDirectory(): string
    {
        return rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'ba';
    }
}
