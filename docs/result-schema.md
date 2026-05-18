# Result Schema

Structora Core discovery results use a versioned public schema. The current schema is:

Release tag: `v0.1.0-alpha`

```json
{
  "schema_version": "0.1.0-alpha",
  "generated_at": "2026-05-19T00:00:00+00:00",
  "status": true,
  "source": "examples/fixtures/synthetic-auth-flow.html",
  "title": "Synthetic Authentication Flow",
  "summary": {
    "engine": "structora-core",
    "mode": "read_only",
    "message": "Discovery completed from provided HTML input.",
    "title_present": true,
    "form_count": 1,
    "field_count": 2,
    "button_count": 1,
    "link_count": 0,
    "heading_count": 1,
    "signal_count": 1,
    "signal_types": ["auth_like_form"],
    "workflow_count": 2,
    "workflow_types": ["auth_flow", "form_flow"]
  },
  "metadata": {
    "read_only": true,
    "execution_required": false,
    "network_access": false,
    "filesystem_writes": false
  },
  "rendering": {
    "rendered": false,
    "strategy": "none",
    "duration_ms": 0,
    "document_length": 0,
    "metadata": {
      "read_only": true,
      "non_executable": true
    },
    "errors": []
  },
  "forms": [],
  "links": [],
  "headings": [],
  "signals": [],
  "signal_summary": {
    "count": 0,
    "types": [],
    "confidence": {},
    "read_only": true,
    "non_executable": true
  },
  "workflow": [],
  "workflow_summary": {
    "workflow_count": 0,
    "workflow_types": [],
    "confidence_summary": {},
    "read_only": true,
    "non_executable": true
  },
  "interpretation": [],
  "extensions_applied": [],
  "export_metadata": {
    "exportable": true,
    "formats": ["json", "summary", "markdown"],
    "read_only": true,
    "non_executable": true
  },
  "enrichment_metadata": {
    "applied_count": 0,
    "read_only": true,
    "non_destructive": true,
    "extensions": []
  }
}
```

## Stability Rules

- All top-level keys are always present.
- Missing collections are represented as empty arrays, never `null`.
- `schema_version` is present on every result.
- `generated_at` is an ISO-8601 timestamp generated at discovery time.
- `metadata.read_only` is always `true`.
- `metadata.execution_required` is always `false`.
- Signals and workflows are observational only and non-executable.

## Top-Level Keys

| Key | Type | Description |
| --- | --- | --- |
| `schema_version` | string | Public result schema version. |
| `generated_at` | string | UTC generation timestamp. |
| `status` | bool | Discovery status. |
| `source` | string | Caller-provided source label or local file path. |
| `title` | string | Parsed document title when available. |
| `summary` | object | Counts and high-level discovery totals. |
| `metadata` | object | Safety, engine, parser, detector, and mapper metadata. |
| `rendering` | object | Passive rendering status, strategy, duration, and metadata. |
| `forms` | array | Parsed form structures. |
| `links` | array | Parsed links. |
| `headings` | array | Parsed headings. |
| `signals` | array | Passive structural signals. |
| `signal_summary` | object | Signal counts, types, and confidence values. |
| `workflow` | array | Observed high-level workflow states. |
| `workflow_summary` | object | Workflow counts, types, and confidence values. |
| `interpretation` | array | Optional interpretation provider output. |
| `extensions_applied` | array | Ordered extension class names applied to the result. |
| `export_metadata` | object | Supported export formats and export safety metadata. |
| `enrichment_metadata` | object | Extension pipeline metadata. |

## Malformed HTML

Malformed HTML is parsed with libxml internal error handling. Parser warnings are counted in metadata as `libxml_error_count`, but discovery still returns the stable result schema.
