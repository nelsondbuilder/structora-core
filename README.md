# Structora Core

[![CI](https://github.com/structora/core/actions/workflows/ci.yml/badge.svg)](https://github.com/structora/core/actions/workflows/ci.yml)
[![Security Check](https://github.com/structora/core/actions/workflows/security-check.yml/badge.svg)](https://github.com/structora/core/actions/workflows/security-check.yml)
[![Release](https://img.shields.io/badge/release-v0.1.0--alpha-blue)](https://github.com/structora/core/releases)

Structora Core is a read-only structure intelligence and workflow discovery framework for developer infrastructure. It analyzes provided HTML input, optionally records passive rendered DOM metadata, detects structural signals, maps workflow states, and exports stable discovery results.

Current release: `v0.1.0-alpha`  
Schema version: `0.1.0-alpha`

## Project Vision

Structora Core exists to make web interface structure understandable without turning analysis into automation. It is designed for developer tools, CI checks, documentation systems, and workflow intelligence pipelines that need stable, public-safe discovery output.

## What It Does

- Parses provided HTML strings or local HTML files
- Extracts forms, fields, buttons, links, headings, title, and metadata
- Detects passive structural signals
- Maps high-level workflow states
- Records passive rendering metadata when explicitly enabled
- Runs read-only extension enrichers
- Exports deterministic JSON, summary text, and Markdown

## What It Does Not Do

- Does not fetch remote URLs through the public CLI
- Does not submit forms
- Does not drive browsers
- Does not execute workflows
- Does not process payments
- Does not solve challenges
- Does not bypass authentication, sessions, or access controls

## Architecture Overview

```text
Input HTML or local file
  -> optional passive rendering
  -> StructureParser
  -> PassiveSignalDetector
  -> WorkflowMapper
  -> ExtensionPipeline
  -> DiscoveryResult
  -> optional Exporter
```

## Workflow Intelligence

Structora maps observational workflow states such as `search_flow`, `auth_flow`, `multi_step_flow`, `challenge_flow`, `confirmation_flow`, `navigation_hub`, `form_flow`, and `informational_page`. These states are not automation steps.

## Rendering Overview

Rendering is passive snapshot acquisition. `StaticHtmlRenderer` normalizes provided HTML locally. The optional Playwright adapter is a disabled scaffold and must remain non-interactive.

## Extension System

Extensions enrich discovery results with metadata and summaries. They are immutable, read-only, and non-destructive. Example extensions demonstrate signal summary, workflow summary, and metadata enrichment.

## Exporters

Built-in exporters:

- `JsonExporter`
- `SummaryExporter`
- `MarkdownExporter`

Exporters transform `DiscoveryResult` into deterministic read-only output.

## CLI Examples

```bash
php bin/structora version
php bin/structora help
php bin/structora discover-file examples/fixtures/synthetic-auth-flow.html
php bin/structora inspect-file examples/fixtures/synthetic-search-flow.html
php bin/structora summary-file examples/fixtures/synthetic-auth-flow.html
php bin/structora render-file examples/fixtures/rendered-search-page.html
php bin/structora inspect-render examples/fixtures/rendered-search-page.html
php bin/structora export-json examples/fixtures/synthetic-auth-flow.html
php bin/structora export-markdown examples/fixtures/synthetic-search-flow.html
```

CLI commands read local files only.

## PHP Example

```php
use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;

$result = (new DiscoveryEngine())->discover(
    '<html><body><h1>Hello</h1></body></html>',
    DiscoveryOptions::fromArray(['source' => 'inline-example'])
);

$payload = $result->toArray();
```

## Result Schema

Every discovery result includes schema and release metadata:

- `schema_version`
- `generated_at`
- `status`
- `source`
- `title`
- `summary`
- `metadata`
- `rendering`
- `forms`
- `links`
- `headings`
- `signals`
- `signal_summary`
- `workflow`
- `workflow_summary`
- `extensions_applied`
- `export_metadata`
- `enrichment_metadata`

## Safety Guarantees

Structora Core is passive by design. It performs no workflow execution, no form submission, no challenge solving, no bypass behavior, and no browser automation.

## Testing

```bash
composer dump-autoload -o
vendor/bin/phpunit
php scripts/php-syntax-check.php
php scripts/repository-hygiene.php
php scripts/security-check.php
php scripts/validate-cli-json.php
```

## Contribution

Contributions should preserve the read-only safety model, use synthetic fixtures, include tests, and update docs for public API or schema changes.

## Roadmap

- Harden alpha feedback from `v0.1.0-alpha`
- Expand passive rendered DOM acquisition adapters
- Add richer schema examples and integration recipes
- Continue improving extension and exporter ergonomics
- Prepare beta stability rules after public feedback

## Release Philosophy

Structora Core follows semantic versioning expectations. Alpha releases may evolve APIs and schema, but every public schema change must be versioned, documented, and tested.

## License

MIT.
