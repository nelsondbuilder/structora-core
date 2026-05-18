<?php

declare(strict_types=1);

namespace Structora\Export;

use Structora\Core\DiscoveryResult;

final class MarkdownExporter implements ExporterInterface
{
    public function export(DiscoveryResult $result, array $options = []): ExportResult
    {
        $payload = $result->toArray();
        $lines = [
            '# Structora Discovery Summary',
            '',
            '| Field | Value |',
            '| --- | --- |',
            '| Source | `' . $payload['source'] . '` |',
            '| Title | ' . ($payload['title'] !== '' ? $payload['title'] : '(none)') . ' |',
            '| Schema | `' . $payload['schema_version'] . '` |',
            '| Read-only | true |',
            '',
            '## Signals',
            '',
            $this->listItems($payload['signal_summary']['types']),
            '',
            '## Workflows',
            '',
            $this->listItems($payload['workflow_summary']['workflow_types']),
        ];

        return new ExportResult(
            format: 'markdown',
            content: implode(PHP_EOL, $lines) . PHP_EOL,
            metadata: [
                'schema_version' => $result->schemaVersion,
                'preserves_metadata' => true,
            ],
        );
    }

    private function listItems(array $items): string
    {
        if ($items === []) {
            return '- none';
        }

        return implode(PHP_EOL, array_map(
            static fn (string $item): string => '- `' . $item . '`',
            $items,
        ));
    }
}
