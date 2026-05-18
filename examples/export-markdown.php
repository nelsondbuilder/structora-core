<?php

declare(strict_types=1);

use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;
use Structora\Export\MarkdownExporter;

require dirname(__DIR__) . '/vendor/autoload.php';

$html = file_get_contents(__DIR__ . '/fixtures/synthetic-search-flow.html') ?: '';
$result = (new DiscoveryEngine())->discover($html, DiscoveryOptions::fromArray([
    'source' => 'synthetic-search-flow',
]));

echo (new MarkdownExporter())->export($result)->content;
