<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$targets = ['src', 'tests', 'examples', 'bin', 'scripts'];
$failures = [];

foreach ($targets as $target) {
    $path = $root . DIRECTORY_SEPARATOR . $target;
    if (!is_dir($path) && !is_file($path)) {
        continue;
    }

    $files = is_file($path)
        ? [new SplFileInfo($path)]
        : new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));

    foreach ($files as $file) {
        if (!$file instanceof SplFileInfo || !isPhpFile($file)) {
            continue;
        }

        $command = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname());
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $failures[] = $file->getPathname();
            echo implode(PHP_EOL, $output) . PHP_EOL;
        }
    }
}

if ($failures !== []) {
    fwrite(STDERR, 'PHP syntax check failed for: ' . implode(', ', $failures) . PHP_EOL);
    exit(1);
}

echo "PHP syntax check passed.\n";

function isPhpFile(SplFileInfo $file): bool
{
    if ($file->getExtension() === 'php') {
        return true;
    }

    $handle = fopen($file->getPathname(), 'rb');
    if ($handle === false) {
        return false;
    }

    $prefix = fread($handle, 256);
    fclose($handle);

    return is_string($prefix) && (str_contains($prefix, '<?php') || str_starts_with($prefix, '#!/usr/bin/env php'));
}
