# Workflow Model

Workflow mapping is observational. It describes high-level workflow states inferred from parsed structure and passive signals.

Supported state types:

- `informational_page`
- `search_flow`
- `auth_flow`
- `multi_step_flow`
- `challenge_flow`
- `confirmation_flow`
- `navigation_hub`
- `form_flow`

Each workflow state includes:

```json
{
  "type": "auth_flow",
  "confidence": 0.95,
  "evidence": {
    "source_signal": "auth_like_form"
  },
  "metadata": {
    "read_only": true,
    "observational_only": true,
    "non_executable": true
  }
}
```

Workflow states are not automation steps. They do not contain click paths, browser instructions, credential inference, or execution plans.
