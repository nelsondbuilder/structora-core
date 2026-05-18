<?php

declare(strict_types=1);

use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;

require dirname(__DIR__) . '/vendor/autoload.php';

$html = file_get_contents(__DIR__ . '/fixtures/synthetic-multi-step-page.html');
$engine = new DiscoveryEngine();
$result = $engine->discover(
    $html ?: '',
    DiscoveryOptions::fromArray([
        'source' => 'synthetic-multi-step-page',
        'metadata' => [
            'example' => 'parse-document',
        ],
    ])
);
$payload = $result->toArray();

echo json_encode([
    'summary' => $payload['summary'],
    'signal_summary' => $payload['signal_summary'],
    'signals' => $payload['signals'],
    'workflow_summary' => $payload['workflow_summary'],
    'workflow' => $payload['workflow'],
    'forms' => $payload['forms'],
    'headings' => $payload['headings'],
    'links' => $payload['links'],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
