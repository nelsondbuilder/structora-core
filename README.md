# Structora Core

Structure intelligence for the web.

Structora Core is a read-only structure intelligence and workflow discovery engine for web interfaces. It is designed to analyze document structure, interaction surfaces, rendered-page signals, and workflow states without performing user actions.

## What Structora Core Does

- Provides a framework-grade foundation for structure discovery
- Models discovery input and output with stable value objects
- Exposes extension points for renderers, enrichers, and interpretation providers
- Supports safe, read-only infrastructure workflows
- Produces structured discovery results for developer tooling and analysis systems

## What Structora Core Does Not Do

- Does not submit forms
- Does not automate checkout
- Does not process payments
- Does not solve captchas
- Does not bypass access controls
- Does not operate as a browser bot
- Does not include private commercial AI logic

## Installation

```bash
composer require structora/core
```

## Quick Start

```php
use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;

$engine = new DiscoveryEngine();

$result = $engine->discover(
    '<html><body><h1>Hello</h1></body></html>',
    DiscoveryOptions::fromArray([
        'source' => 'synthetic-example',
    ])
);

print_r($result->toArray());
```

## Safety Model

Structora Core is passive by design. It observes structure and returns discovery output.

## License

MIT.
