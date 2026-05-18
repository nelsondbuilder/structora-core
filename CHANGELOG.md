# Changelog

All notable changes to Structora Core are documented in this file.

The format follows Keep a Changelog, and this project follows semantic versioning expectations.

## [Unreleased]

- No unreleased changes yet.

## [0.1.0-alpha] - 2026-05-19

First public alpha release.

### Core Discovery

- Added versioned `DiscoveryResult` schema.
- Added `DiscoveryEngine` read-only discovery pipeline.
- Added `DiscoveryOptions` and stable public result metadata.
- Added malformed HTML tolerance.

### Structure Parsing

- Added `StructureParserInterface`.
- Added `StructureParser` using `DOMDocument` and `DOMXPath`.
- Added parsed document, form, field, link, and heading value objects.

### Passive Signals

- Added `PassiveSignalDetector`.
- Added signal collection and confidence/evidence metadata.
- Added passive detection for auth-like forms, search forms, multi-step indicators, navigation-heavy pages, density signals, challenge indicators, confirmation indicators, and progress indicators.

### Workflow Intelligence

- Added `WorkflowMapper`.
- Added workflow states and workflow summaries.
- Added support for informational, search, auth, multi-step, challenge, confirmation, navigation hub, and form flows.

### Rendering

- Added passive rendering contracts.
- Added `StaticHtmlRenderer` and `NullRenderer`.
- Added disabled Playwright adapter scaffold with explicit read-only boundaries.

### Exporters and Extensions

- Added JSON, summary text, and Markdown exporters.
- Added immutable extension pipeline.
- Added example metadata, signal summary, and workflow summary extensions.

### CLI

- Added local-file discovery, inspect, summary, render, and export commands.
- Added stable `version` output with release and schema metadata.

### Documentation

- Added result schema, architecture, signal, workflow, rendering, export, extension, integration, development workflow, versioning, release, and project philosophy documentation.

### CI and Safety

- Added GitHub Actions CI across PHP 8.1, 8.2, and 8.3.
- Added security check workflow.
- Added repository hygiene, syntax, security, and CLI JSON validation scripts.
- Added issue templates, PR template, CODEOWNERS, and contribution workflow notes.

### Safety Milestones

- Confirmed public project remains read-only and observational.
- Explicitly excluded automation, browser botting, scraping bypasses, credential tooling, payment execution, challenge solving, and workflow execution.
