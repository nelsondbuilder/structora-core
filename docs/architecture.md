# Architecture

Structora Core is a read-only structure intelligence pipeline for provided HTML input.

```text
HTML string or local file
  -> optional passive rendering
  -> StructureParser
  -> PassiveSignalDetector
  -> WorkflowMapper
  -> optional enrichers
  -> optional interpretation provider
  -> DiscoveryResult
```

## Components

- `StructureParserInterface`: parses a provided HTML string with `DOMDocument` and `DOMXPath`.
- `RendererInterface`: optionally returns a read-only rendered HTML snapshot and render metadata.
- `DetectorInterface`: detects passive structural signals from a parsed document.
- `WorkflowMapperInterface`: maps parsed structure and signals into high-level workflow states.
- `DiscoveryEngine`: orchestrates parsing, detection, workflow mapping, enrichers, and optional interpretation.
- `DiscoveryResult`: owns the public, versioned result schema.

## Safety Boundaries

Structora Core does not fetch remote URLs, submit forms, click buttons, drive browsers, solve challenges, execute workflows, or bypass access controls. CLI commands read local files only.

## Extension Philosophy

Extensions should enrich read-only discovery output. They should preserve the public schema, avoid side effects, and keep generated metadata explicit about safety and provenance.
