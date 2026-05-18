# Architecture

Structora Core follows a read-only discovery pipeline:

```text
Input Document -> Structure Analysis -> Signal Detection -> Workflow Mapping -> Result Enrichment -> Discovery Result
```

This scaffold defines the public architecture boundaries. Concrete DOM parsing, detection, workflow mapping, and rendering implementations are added incrementally behind stable interfaces.
