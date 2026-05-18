# Export Model

Structora exporters convert `DiscoveryResult` objects into deterministic read-only output formats.

## Built-In Exporters

- `JsonExporter`: full discovery result as pretty JSON.
- `SummaryExporter`: compact plain-text summary.
- `MarkdownExporter`: developer-friendly Markdown summary.

Exporters do not mutate discovery results. They preserve metadata and produce `ExportResult` objects with format, content, and export metadata.

## Custom Exporter Example

```php
use Structora\Core\DiscoveryResult;
use Structora\Export\ExporterInterface;
use Structora\Export\ExportResult;

final class CsvSummaryExporter implements ExporterInterface
{
    public function export(DiscoveryResult $result, array $options = []): ExportResult
    {
        $payload = $result->toArray();
        $content = "source,title\n"
            . $payload['source'] . "," . $payload['title'] . "\n";

        return new ExportResult('csv', $content, [
            'read_only' => true,
            'preserves_metadata' => true,
        ]);
    }
}
```

## CLI

```bash
php bin/structora export-json examples/fixtures/synthetic-auth-flow.html
php bin/structora export-summary examples/fixtures/synthetic-auth-flow.html
php bin/structora export-markdown examples/fixtures/synthetic-search-flow.html
```

All commands read local files only.
