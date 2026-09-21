<?php

use CodeIgniter\Test\CIUnitTestCase;

/** @internal */
final class ProductionSurfaceTest extends CIUnitTestCase
{
    public function testPublicPhpSurfaceContainsOnlyFrontController(): void
    {
        $phpFiles = glob(PUBLICPATH . '*.php') ?: [];
        $names = array_map('basename', $phpFiles);
        sort($names);

        $this->assertSame(['index.php'], $names, 'Debug PHP probes must not be deployed inside the public document root.');
    }

    public function testBaStorageDirectoryIsOutsidePublicRoot(): void
    {
        $uploadPath = realpath(WRITEPATH) . DIRECTORY_SEPARATOR . 'uploads';
        $publicPath = rtrim((string) realpath(PUBLICPATH), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->assertFalse(str_starts_with($uploadPath . DIRECTORY_SEPARATOR, $publicPath));
    }
}
