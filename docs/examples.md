# Examples

## CLI

```bash
php bin/structora version
php bin/structora discover-file examples/fixtures/synthetic-auth-flow.html
php bin/structora inspect-file examples/fixtures/synthetic-search-flow.html
php bin/structora summary-file examples/fixtures/synthetic-auth-flow.html
```

All file commands accept local file paths only.

## PHP API

```php
use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;

$engine = new DiscoveryEngine();
$result = $engine->discover(
    '<html><body><h1>Example</h1></body></html>',
    DiscoveryOptions::fromArray(['source' => 'inline-example'])
);

$payload = $result->toArray();
```

## Malformed HTML

```php
$result = $engine->discover('<title>Broken</title><h1>Open<form><input name="q">');
```

The result still includes stable top-level keys, safety metadata, and parser error counts.
