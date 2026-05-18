<?php

declare(strict_types=1);

use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;
use Structora\Export\JsonExporter;

require dirname(__DIR__) . '/vendor/autoload.php';

$html = file_get_contents(__DIR__ . '/fixtures/synthetic-auth-flow.html') ?: '';
$result = (new DiscoveryEngine())->discover($html, DiscoveryOptions::fromArray([
    'source' => 'synthetic-auth-flow',
]));

echo (new JsonExporter())->export($result)->content;
