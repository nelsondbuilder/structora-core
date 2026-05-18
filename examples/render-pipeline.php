<?php

declare(strict_types=1);

use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;
use Structora\Rendering\StaticHtmlRenderer;

require dirname(__DIR__) . '/vendor/autoload.php';

$html = file_get_contents(__DIR__ . '/fixtures/rendered-search-page.html') ?: '';
$engine = (new DiscoveryEngine())->withRenderer(new StaticHtmlRenderer());
$result = $engine->discover(
    $html,
    DiscoveryOptions::fromArray([
        'source' => 'rendered-search-page',
        'rendering_enabled' => true,
        'metadata' => [
            'example' => 'render-pipeline',
        ],
    ])
);

$payload = $result->toArray();

echo json_encode([
    'summary' => $payload['summary'],
    'rendering' => $payload['rendering'],
    'signals' => $payload['signal_summary'],
    'workflow' => $payload['workflow_summary'],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
