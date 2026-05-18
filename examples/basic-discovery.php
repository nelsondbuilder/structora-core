<?php

declare(strict_types=1);

use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;

require dirname(__DIR__) . '/vendor/autoload.php';

$html = file_get_contents(__DIR__ . '/fixtures/synthetic-basic-page.html');

$engine = new DiscoveryEngine();

$result = $engine->discover(
    $html ?: '',
    DiscoveryOptions::fromArray([
        'source' => 'synthetic-basic-page',
        'metadata' => [
            'example' => 'basic-discovery',
        ],
    ])
);

echo json_encode($result->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
