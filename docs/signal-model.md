# Signal Model

Passive signals describe structural observations from a parsed document. They are not actions and do not imply execution.

Supported signal families include:

- `auth_like_form`
- `search_form`
- `multi_step_indicator`
- `navigation_heavy_page`
- `form_density`
- `input_density`
- `button_density`
- `challenge_indicator`
- `confirmation_indicator`
- `progress_indicator`

Each signal includes:

```json
{
  "type": "challenge_indicator",
  "confidence": 0.88,
  "evidence": {
    "matched_terms": ["challenge", "verification"]
  },
  "metadata": {
    "read_only": true,
    "observational_only": true,
    "non_executable": true
  }
}
```

Signals must remain passive. They must not submit forms, solve challenges, simulate clicks, infer credentials, or generate automation instructions.
