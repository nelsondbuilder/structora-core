# Contributing to Structora Core

Thank you for helping improve Structora Core.

## Project Scope

Structora Core accepts contributions related to:

- Read-only structure discovery
- DOM and document analysis
- Workflow mapping abstractions
- Rendering interfaces
- Interpretation interfaces
- Result schemas
- Developer tooling
- Synthetic fixtures and tests

## Not Accepted

The public project does not accept contributions for:

- Checkout automation
- Captcha solving
- Payment execution
- Credentialed scraping
- Access-control bypasses
- Browser bot workflows
- Real client/customer/storefront data fixtures

## Development Setup

```bash
composer install
npm install
C:\xampp\php\php.exe vendor\bin\phpunit
```

## Developer Workflow

- Keep fixtures synthetic and public-safe.
- Run `composer dump-autoload -o` after adding public classes.
- Run PHPUnit before opening a pull request.
- Update docs when public schema, CLI output, or extension points change.
- Do not commit generated caches or local runtime artifacts.

## Coding Standards

- PHP 8.1+
- PSR-4 autoloading
- Typed properties where useful
- Small, explicit interfaces
- No secrets
- No generated runtime data

## Versioning and Stability

Structora Core follows semantic versioning expectations. During `0.x`, public APIs can evolve, but result schema changes must be explicit, documented, and covered by tests. Every discovery payload includes a `schema_version` so downstream tools can handle changes deliberately.

Stable schema sections should prefer empty arrays and stable metadata keys over omitted values or `null`.

## Pull Request Checklist

- No secrets, logs, cookies, screenshots, traces, HAR files, or generated collections
- No private/commercial logic
- No automation/payment/captcha behavior
- Tests or placeholder coverage plan included
- Documentation updated when public API changes
