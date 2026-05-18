<?php

declare(strict_types=1);

namespace Structora\Export;

use JsonException;
use Structora\Core\DiscoveryResult;

final class JsonExporter implements ExporterInterface
{
    /**
     * @throws JsonException
     */
    public function export(DiscoveryResult $result, array $options = []): ExportResult
    {
        $content = json_encode(
            $result->toArray(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );

        return new ExportResult(
            format: 'json',
            content: $content . PHP_EOL,
            metadata: [
                'schema_version' => $result->schemaVersion,
                'preserves_metadata' => true,
            ],
        );
    }
}
