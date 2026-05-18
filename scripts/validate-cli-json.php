<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$commands = [
    ['version'],
    ['summary-file', 'examples/fixtures/synthetic-auth-flow.html'],
    ['inspect-file', 'examples/fixtures/synthetic-search-flow.html'],
    ['render-file', 'examples/fixtures/rendered-search-page.html'],
    ['inspect-render', 'examples/fixtures/rendered-search-page.html'],
    ['export-json', 'examples/fixtures/synthetic-auth-flow.html'],
];

foreach ($commands as $parts) {
    $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($root . '/bin/structora');
    foreach ($parts as $part) {
        $command .= ' ' . escapeshellarg($part);
    }

    exec($command, $output, $exitCode);
    $json = implode(PHP_EOL, $output);
    $output = [];

    if ($exitCode !== 0) {
        fwrite(STDERR, "CLI command failed: " . implode(' ', $parts) . PHP_EOL);
        exit(1);
    }

    json_decode($json, true, 512, JSON_THROW_ON_ERROR);
}

echo "CLI JSON validation passed.\n";
