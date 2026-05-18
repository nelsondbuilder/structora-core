# Public API Stability

Structora Core public APIs include:

- `DiscoveryEngine`
- `DiscoveryOptions`
- `DiscoveryResult`
- parser, detector, renderer, workflow, exporter, and extension interfaces
- CLI JSON output
- discovery result schema

For `v0.1.0-alpha`, these APIs are suitable for experimentation and early integration. Breaking changes may occur before beta, but they should be documented in the changelog and reflected in schema versioning.

## Stability Expectations

- Stable top-level result keys.
- Empty arrays instead of `null`.
- Explicit release and schema metadata.
- Tests for public schema and CLI output.
- Documentation for public-facing changes.
