<?php

use App\Libraries\BaDocumentStorage;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class BaDocumentStorageTest extends CIUnitTestCase
{
    public function testAllowsDetectedPdfAndUsesSafeStorageExtension(): void
    {
        $storage = new BaDocumentStorage();

        $this->assertNull($storage->validateDetectedFile('application/pdf', 1024));
        $this->assertSame('pdf', $storage->downloadExtension('0123456789abcdef0123456789abcdef.pdf'));
    }

    public function testRejectsDetectedPhpContentRegardlessOfClientFilename(): void
    {
        $this->assertSame(
            'Dokumen BA hanya menerima PDF, JPG, atau PNG yang valid.',
            (new BaDocumentStorage())->validateDetectedFile('text/x-php', 1024),
        );
    }

    public function testRejectsOversizedDocumentBeforeStorage(): void
    {
        $this->assertSame(
            'Dokumen BA maksimal berukuran 5 MB.',
            (new BaDocumentStorage())->validateDetectedFile('application/pdf', BaDocumentStorage::MAX_SIZE_BYTES + 1),
        );
    }

    public function testLegacyUnsafeExtensionDownloadsAsBinary(): void
    {
        $this->assertSame('bin', (new BaDocumentStorage())->downloadExtension('legacy-document.php'));
    }

    public function testStoresDetectedImageOutsidePublicRootWithServerGeneratedName(): void
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'ba-upload-');
        $this->assertNotFalse($temporaryPath);
        file_put_contents($temporaryPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true));

        $storage = new BaDocumentStorage();
        $storageKey = null;
        try {
            $file = new class(
                $temporaryPath,
                'berita-acara.php',
                'application/x-httpd-php',
                filesize($temporaryPath),
                UPLOAD_ERR_OK,
            ) extends UploadedFile {
                public function isValid(): bool
                {
                    return $this->error === UPLOAD_ERR_OK && is_file($this->path);
                }

                public function move(string $targetPath, ?string $name = null, bool $overwrite = false): bool
                {
                    if (!is_dir($targetPath)) {
                        mkdir($targetPath, 0777, true);
                    }
                    $destination = rtrim($targetPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
                    $this->hasMoved = rename($this->path, $destination);
                    $this->path = $destination;
                    return $this->hasMoved;
                }
            };
            $storageKey = $storage->store($file);
            $resolved = $storage->resolve($storageKey);

            $this->assertMatchesRegularExpression('/^[a-f0-9]{32}\.png$/', $storageKey);
            $this->assertNotNull($resolved);
            $this->assertStringStartsWith(rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads', $resolved);
            $this->assertStringNotContainsString(rtrim(FCPATH, DIRECTORY_SEPARATOR), $resolved);
        } finally {
            if (is_string($storageKey)) {
                $storage->delete($storageKey);
            }
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
        }
    }
}
