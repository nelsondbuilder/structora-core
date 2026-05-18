<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$patterns = [
    '/(^|\/|\\\\)\.env(\.|$)/i',
    '/(^|\/|\\\\)node_modules(\/|\\\\|$)/i',
    '/(^|\/|\\\\)vendor(\/|\\\\|$)/i',
    '/(^|\/|\\\\)\.phpunit\.cache(\/|\\\\|$)/i',
    '/\.(har|log|trace|cookie|cookies|sqlite|db)$/i',
    '/(^|\/|\\\\)(screenshots?|traces?|logs?|cookies?)(\/|\\\\|$)/i',
];

$files = trackedFiles($root);
$violations = [];

foreach ($files as $file) {
    $normalized = str_replace('\\', '/', $file);
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $normalized) === 1) {
            $violations[] = $file;
            break;
        }
    }
}

if ($violations !== []) {
    fwrite(STDERR, "Forbidden runtime artifacts are tracked:\n");
    fwrite(STDERR, implode(PHP_EOL, array_unique($violations)) . PHP_EOL);
    exit(1);
}

echo "Repository hygiene check passed.\n";

function trackedFiles(string $root): array
{
    $command = 'git -C ' . escapeshellarg($root) . ' ls-files';
    exec($command, $output, $exitCode);

    if ($exitCode !== 0) {
        fwrite(STDERR, "Unable to inspect tracked files with git ls-files.\n");
        exit(1);
    }

    return $output;
}
