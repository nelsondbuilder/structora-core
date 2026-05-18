# Extension API

Structora Core exposes public interfaces for safe extension:

- `ExtensionInterface`
- `ResultEnricherInterface`
- `RendererInterface`
- `InterpretationProviderInterface`

Extensions must preserve Structora Core's read-only safety model.
