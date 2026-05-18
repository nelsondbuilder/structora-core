<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$secretTargets = ['src', 'bin', 'scripts'];
$runtimeTargets = ['src'];
$secretPatterns = [
    '/-----BEGIN (RSA |OPENSSH |EC |DSA )?PRIVATE KEY-----/',
    '/\bAKIA[0-9A-Z]{16}\b/',
    '/\bghp_[A-Za-z0-9_]{36,}\b/',
    '/\bsk-[A-Za-z0-9]{20,}\b/',
    '/password\s*=\s*[\'"][^\'"]+[\'"]/i',
    '/api[_-]?key\s*=\s*[\'"][^\'"]+[\'"]/i',
];
$runtimePatterns = [
    '/curl_exec\s*\(/i',
    '/fsockopen\s*\(/i',
    '/pfsockopen\s*\(/i',
    '/stream_socket_client\s*\(/i',
    '/\b(remote|http|https):\/\//i',
    '/\b(submitForm|form\.submit|click\s*\(|browser\.|webdriver|playwright|selenium|puppeteer)\b/i',
    '/\b(proxy|session bypass|auth bypass|payment execution|checkout automation)\b/i',
];

$violations = [];

foreach (implementationFiles($root, $secretTargets) as $file) {
    $contents = file_get_contents($file);
    if ($contents === false) {
        continue;
    }

    foreach ($secretPatterns as $pattern) {
        if (preg_match($pattern, $contents) === 1) {
            $violations[] = relativePath($root, $file) . ' matched secret pattern';
        }
    }
}

foreach (implementationFiles($root, $runtimeTargets) as $file) {
    $contents = file_get_contents($file);
    if ($contents === false) {
        continue;
    }

    foreach ($runtimePatterns as $pattern) {
        if (preg_match($pattern, $contents) === 1) {
            $violations[] = relativePath($root, $file) . ' matched forbidden runtime pattern';
        }
    }
}

if ($violations !== []) {
    fwrite(STDERR, "Security check failed:\n");
    fwrite(STDERR, implode(PHP_EOL, array_unique($violations)) . PHP_EOL);
    exit(1);
}

echo "Security check passed.\n";

function implementationFiles(string $root, array $targets): array
{
    $files = [];

    foreach ($targets as $target) {
        $path = $root . DIRECTORY_SEPARATOR . $target;
        if (is_file($path)) {
            $files[] = $path;
            continue;
        }

        if (!is_dir($path)) {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && in_array($file->getExtension(), ['php', ''], true)) {
                $files[] = $file->getPathname();
            }
        }
    }

    return $files;
}

function relativePath(string $root, string $path): string
{
    return ltrim(str_replace('\\', '/', substr($path, strlen($root))), '/');
}
