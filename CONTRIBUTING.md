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
```

## Coding Standards

- PHP 8.1+
- PSR-4 autoloading
- Typed properties where useful
- Small, explicit interfaces
- No secrets
- No generated runtime data

## Pull Request Checklist

- No secrets, logs, cookies, screenshots, traces, HAR files, or generated collections
- No private/commercial logic
- No automation/payment/captcha behavior
- Tests or placeholder coverage plan included
- Documentation updated when public API changes
