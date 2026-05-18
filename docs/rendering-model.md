# Rendering Model

Structora Core rendering is a passive acquisition layer for rendered DOM analysis. It exists so discovery can analyze a rendered HTML snapshot while preserving the same read-only safety model as static parsing.

## Supported Renderers

- `NullRenderer`: safe default that performs no rendering.
- `StaticHtmlRenderer`: accepts caller-provided HTML, normalizes it, and returns a `RenderResult`.
- `Adapters\PlaywrightRenderer`: optional scaffold only. It is disabled by default and does not include a runtime implementation.

## Render Result

Render output is represented by:

- `RenderedDocument`: rendered HTML snapshot and source metadata.
- `RenderedMetadata`: strategy, duration, safety details, and rendered flag.
- `RenderResult`: status, document, metadata, and errors.

## Safety Guarantees

Structora rendering is:

- passive
- observational
- read-only
- non-executable

Structora rendering is not:

- browser automation
- workflow execution
- interaction automation
- form submission
- challenge solving
- access bypass tooling

## Optional Adapter Boundary

The Playwright adapter is an explicit scaffold for future local-only rendered DOM acquisition. A real adapter may open a page, wait for DOM readiness, extract the rendered HTML snapshot, and return that snapshot. It must not click, type, submit forms, navigate multi-step flows, solve challenges, or mutate page state.

## CLI

```bash
php bin/structora render-file examples/fixtures/rendered-search-page.html
php bin/structora inspect-render examples/fixtures/rendered-search-page.html
```

Both commands accept local files only.
