# Developer Integration

Structora Core can be embedded in developer tooling, CI checks, documentation generators, and internal analysis systems that need read-only workflow intelligence.

## Discovery and Export

```php
use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;
use Structora\Export\JsonExporter;

$result = (new DiscoveryEngine())->discover(
    $html,
    DiscoveryOptions::fromArray(['source' => 'inline-html'])
);

$json = (new JsonExporter())->export($result)->content;
```

## Extension Pipeline

```php
use Structora\Extension\Examples\SignalSummaryExtension;

$engine = (new DiscoveryEngine())
    ->withResultEnricher(new SignalSummaryExtension());
```

Extensions add metadata and summaries. They never execute workflows or interact with pages.

## Integration Boundaries

Structora is suitable for:

- read-only structure analysis
- schema-stable discovery exports
- synthetic fixture validation
- developer documentation generation
- workflow-state intelligence

Structora is not suitable for:

- browser automation
- scraping bypass workflows
- form submission
- payment or checkout execution
- challenge solving
