<?php

declare(strict_types=1);

namespace Structora\Export;

use Structora\Core\DiscoveryResult;

final class SummaryExporter implements ExporterInterface
{
    public function export(DiscoveryResult $result, array $options = []): ExportResult
    {
        $payload = $result->toArray();
        $lines = [
            'Structora Discovery Summary',
            'Source: ' . $payload['source'],
            'Title: ' . ($payload['title'] !== '' ? $payload['title'] : '(none)'),
            'Forms: ' . $payload['summary']['form_count'],
            'Signals: ' . $payload['summary']['signal_count'] . ' [' . implode(', ', $payload['summary']['signal_types']) . ']',
            'Workflow: ' . $payload['summary']['workflow_count'] . ' [' . implode(', ', $payload['summary']['workflow_types']) . ']',
            'Read-only: true',
        ];

        return new ExportResult(
            format: 'summary',
            content: implode(PHP_EOL, $lines) . PHP_EOL,
            metadata: [
                'schema_version' => $result->schemaVersion,
                'preserves_metadata' => true,
            ],
        );
    }
}
