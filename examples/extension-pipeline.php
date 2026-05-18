<?php

declare(strict_types=1);

use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;
use Structora\Extension\Examples\MetadataEnrichmentExtension;
use Structora\Extension\Examples\SignalSummaryExtension;
use Structora\Extension\Examples\WorkflowSummaryExtension;

require dirname(__DIR__) . '/vendor/autoload.php';

$html = file_get_contents(__DIR__ . '/fixtures/synthetic-auth-flow.html') ?: '';
$engine = (new DiscoveryEngine())
    ->withResultEnricher(new SignalSummaryExtension())
    ->withResultEnricher(new WorkflowSummaryExtension())
    ->withResultEnricher(new MetadataEnrichmentExtension());

$result = $engine->discover($html, DiscoveryOptions::fromArray([
    'source' => 'synthetic-auth-flow',
    'metadata' => [
        'example' => 'extension-pipeline',
    ],
]));

$payload = $result->toArray();

echo json_encode([
    'extensions_applied' => $payload['extensions_applied'],
    'enrichment_metadata' => $payload['enrichment_metadata'],
    'metadata' => [
        'extension_signal_summary' => $payload['metadata']['extension_signal_summary'] ?? [],
        'extension_workflow_summary' => $payload['metadata']['extension_workflow_summary'] ?? [],
        'extension_metadata' => $payload['metadata']['extension_metadata'] ?? [],
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
