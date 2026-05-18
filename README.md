# Structora Core

Structora Core is a read-only structure intelligence and workflow discovery engine for developer infrastructure. It analyzes provided HTML input and returns a stable, versioned discovery result.

## What It Does

- Parses provided HTML strings or local HTML files
- Extracts forms, fields, buttons, links, headings, title, and metadata
- Detects passive structural signals
- Maps high-level workflow states
- Returns a stable `DiscoveryResult` schema

## What It Does Not Do

- Does not fetch remote URLs
- Does not submit forms
- Does not drive a browser
- Does not execute workflows
- Does not process payments
- Does not solve challenges
- Does not bypass authentication or sessions

## Architecture

```text
Input HTML -> StructureParser -> PassiveSignalDetector -> WorkflowMapper -> DiscoveryResult
```

The core pipeline is observational. Optional enrichers and interpretation providers can add read-only context without changing that safety boundary.

## CLI Examples

```bash
php bin/structora version
php bin/structora discover-file examples/fixtures/synthetic-auth-flow.html
php bin/structora inspect-file examples/fixtures/synthetic-search-flow.html
php bin/structora summary-file examples/fixtures/synthetic-auth-flow.html
```

CLI commands read local files only and output JSON.

## PHP Example

```php
use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;

$result = (new DiscoveryEngine())->discover(
    '<html><body><h1>Hello</h1></body></html>',
    DiscoveryOptions::fromArray(['source' => 'inline-example'])
);

print_r($result->toArray());
```

## Result Schema

Every discovery result includes `schema_version`, `generated_at`, `status`, `source`, `title`, `summary`, `metadata`, `forms`, `links`, `headings`, `signals`, `signal_summary`, `workflow`, and `workflow_summary`.

The current schema version is `0.1.0-alpha`.

## Safety Guarantees

Structora Core is passive by design. It performs no network requests, no browser actions, no workflow execution, and no form submission. Fixtures are synthetic and public-safe.

## Extension Philosophy

Extensions should preserve the public schema, avoid side effects, and add developer-facing read-only intelligence. Automation behavior belongs outside Structora Core.

## License

MIT.
