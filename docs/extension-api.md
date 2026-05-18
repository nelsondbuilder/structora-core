# Extension API

Structora Core extensions augment discovery results. They are read-only enrichment hooks, not automation hooks.

## Interfaces

- `ExtensionInterface`: boot-time hook for advanced integrations.
- `ResultEnricherInterface`: transforms a `DiscoveryResult` into a new enriched `DiscoveryResult`.
- `ExtensionPipeline`: runs result enrichers sequentially and records metadata.

## Rules

Extensions must:

- preserve discovery result immutability
- keep enrichment observational
- avoid side effects
- avoid network requests, browser actions, form submission, workflow execution, and bypass behavior
- use synthetic fixtures in tests

## Custom Extension Example

```php
use Structora\Core\DiscoveryOptions;
use Structora\Core\DiscoveryResult;
use Structora\Extension\ResultEnricherInterface;

final class MyMetadataExtension implements ResultEnricherInterface
{
    public function enrich(DiscoveryResult $result, DiscoveryOptions $options): DiscoveryResult
    {
        $metadata = $result->metadata;
        $metadata['my_extension'] = [
            'read_only' => true,
            'note' => 'observational enrichment only',
        ];

        return $result->with(['metadata' => $metadata]);
    }
}
```

## Registration Example

```php
$engine = (new DiscoveryEngine())
    ->withResultEnricher(new MyMetadataExtension());
```

The result will include `extensions_applied` and `enrichment_metadata`.
