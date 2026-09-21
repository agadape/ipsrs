<?php

declare(strict_types=1);

/**
 * Read-only production readiness probe.
 *
 * It never opens the application database and never mutates project data.
 */

$root = dirname(__DIR__);
$env  = readEnvFile($root . DIRECTORY_SEPARATOR . '.env');
$checks = [];

$add = static function (string $status, string $name, string $detail) use (&$checks): void {
    $checks[] = compact('status', 'name', 'detail');
};

$add(version_compare(PHP_VERSION, '8.2.0', '>=') ? 'PASS' : 'FAIL', 'PHP version', PHP_VERSION);

foreach (['intl', 'mbstring', 'fileinfo', 'gd', 'dom', 'xml', 'xmlreader', 'xmlwriter', 'simplexml', 'zlib', 'mysqli', 'zip'] as $extension) {
    $add(extension_loaded($extension) ? 'PASS' : 'FAIL', 'Extension ' . $extension, extension_loaded($extension) ? 'loaded' : 'missing');
}

$environment = envValue($env, 'CI_ENVIRONMENT');
$add($environment === 'production' ? 'PASS' : 'FAIL', 'Environment', $environment ?: 'not configured');

$baseUrl = envValue($env, 'app.baseURL');
$add(str_starts_with(strtolower($baseUrl), 'https://') ? 'PASS' : 'FAIL', 'HTTPS base URL', $baseUrl ?: 'not configured');

$forceHttps = envBool($env, 'app.forceGlobalSecureRequests', phpBoolDefault($root . '/app/Config/App.php', 'forceGlobalSecureRequests'));
$add($forceHttps ? 'PASS' : 'FAIL', 'Force HTTPS', $forceHttps ? 'enabled' : 'disabled');

$secureCookie = envBool($env, 'cookie.secure', phpBoolDefault($root . '/app/Config/Cookie.php', 'secure'));
$httpOnly     = envBool($env, 'cookie.httponly', phpBoolDefault($root . '/app/Config/Cookie.php', 'httponly'));
$sameSite     = envValue($env, 'cookie.samesite') ?: phpStringDefault($root . '/app/Config/Cookie.php', 'samesite');
$add($secureCookie ? 'PASS' : 'FAIL', 'Secure cookie', $secureCookie ? 'enabled' : 'disabled');
$add($httpOnly ? 'PASS' : 'FAIL', 'HTTP-only cookie', $httpOnly ? 'enabled' : 'disabled');
$add(in_array(strtolower($sameSite), ['lax', 'strict'], true) ? 'PASS' : 'FAIL', 'SameSite cookie', $sameSite ?: 'not configured');

$dbUser = envValue($env, 'database.default.username');
$add($dbUser !== '' && strtolower($dbUser) !== 'root' ? 'PASS' : 'FAIL', 'Dedicated database user', $dbUser === '' ? 'not configured' : ($dbUser === 'root' ? 'root is forbidden' : 'configured'));
$dbDebug = envBool($env, 'database.default.DBDebug', phpBoolDefault($root . '/app/Config/Database.php', 'DBDebug'));
$add(!$dbDebug ? 'PASS' : 'FAIL', 'Database debug', $dbDebug ? 'enabled' : 'disabled');

foreach (['writable', 'writable/cache', 'writable/logs', 'writable/session', 'writable/uploads'] as $relativePath) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    $add(is_dir($path) && is_writable($path) ? 'PASS' : 'FAIL', 'Writable path ' . $relativePath, is_dir($path) ? (is_writable($path) ? 'writable' : 'not writable') : 'missing');
}

$publicPhp = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/public', FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
        $publicPhp[] = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
    }
}
sort($publicPhp);
$add($publicPhp === ['public/index.php'] ? 'PASS' : 'FAIL', 'Public PHP surface', implode(', ', $publicPhp) ?: 'none');

$publicRoot = realpath($root . '/public');
$uploadRoot = realpath($root . '/writable/uploads');
$outsidePublic = $publicRoot !== false && $uploadRoot !== false
    && !str_starts_with(strtolower($uploadRoot . DIRECTORY_SEPARATOR), strtolower($publicRoot . DIRECTORY_SEPARATOR));
$add($outsidePublic ? 'PASS' : 'FAIL', 'BA upload isolation', $outsidePublic ? 'outside public root' : 'cannot prove isolation');

$add('MANUAL', 'Web server document root', 'must resolve exactly to the project public/ directory');
$add('MANUAL', 'HTTPS and response headers', 'verify from the deployed URL');
$add('MANUAL', 'Backup restore and MySQL concurrency', 'run on a non-production database with production-equivalent MySQL');

$failures = count(array_filter($checks, static fn (array $check): bool => $check['status'] === 'FAIL'));
foreach ($checks as $check) {
    printf("[%s] %s: %s\n", $check['status'], $check['name'], $check['detail']);
}
printf("\nResult: %d PASS, %d FAIL, %d MANUAL\n",
    count(array_filter($checks, static fn (array $check): bool => $check['status'] === 'PASS')),
    $failures,
    count(array_filter($checks, static fn (array $check): bool => $check['status'] === 'MANUAL')),
);

exit($failures === 0 ? 0 : 1);

/** @return array<string, string> */
function readEnvFile(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $values = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $values[$key] = trim($value, " \t\n\r\0\x0B\"'");
    }

    return $values;
}

/** @param array<string, string> $env */
function envValue(array $env, string $key): string
{
    $runtime = getenv($key);
    return $runtime !== false ? trim((string) $runtime) : trim($env[$key] ?? '');
}

/** @param array<string, string> $env */
function envBool(array $env, string $key, bool $default): bool
{
    $value = envValue($env, $key);
    if ($value === '') {
        return $default;
    }

    return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
}

function phpBoolDefault(string $path, string $property): bool
{
    $contents = is_file($path) ? (string) file_get_contents($path) : '';
    if (preg_match('/public bool \$' . preg_quote($property, '/') . '\s*=\s*(true|false)/i', $contents, $match) === 1) {
        return strtolower($match[1]) === 'true';
    }

    return false;
}

function phpStringDefault(string $path, string $property): string
{
    $contents = is_file($path) ? (string) file_get_contents($path) : '';
    if (preg_match('/public string \$' . preg_quote($property, '/') . '\s*=\s*[\'\"]([^\'\"]*)[\'\"]/', $contents, $match) === 1) {
        return $match[1];
    }

    return '';
}
