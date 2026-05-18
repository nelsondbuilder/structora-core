# Development Workflow

Structora Core is maintained as read-only developer infrastructure. Contributions should keep the discovery pipeline passive, deterministic, and public-safe.

## Local Validation

Windows with XAMPP PHP:

```powershell
C:\xampp\php\php.exe scripts\php-syntax-check.php
C:\xampp\php\php.exe vendor\bin\phpunit
C:\xampp\php\php.exe scripts\repository-hygiene.php
C:\xampp\php\php.exe scripts\security-check.php
C:\xampp\php\php.exe scripts\validate-cli-json.php
```

Unix-like environments:

```bash
php scripts/php-syntax-check.php
php vendor/bin/phpunit
php scripts/repository-hygiene.php
php scripts/security-check.php
php scripts/validate-cli-json.php
```

If `make` is available:

```bash
make validate
```

## CI Pipeline

The main CI workflow runs on pushes and pull requests across PHP 8.1, 8.2, and 8.3. It validates Composer metadata, installs dependencies, dumps optimized autoload files, runs PHPUnit, runs PHP syntax checks, validates CLI JSON output, and checks repository hygiene.

## Security Check

The security workflow scans for accidental secrets, unsafe runtime patterns in implementation source, and forbidden tracked runtime artifacts. It is intentionally conservative around repository hygiene and intentionally focused on keeping the public package read-only.

## Repository Hygiene

Do not commit runtime artifacts such as logs, HAR files, screenshots, traces, cookies, `.env` files, `vendor`, `node_modules`, or generated caches.

## Release Philosophy

Structora Core follows semantic versioning expectations. During the `0.x` line, schema and API changes are allowed but must be explicit, documented, and covered by tests. Every result includes `schema_version` so downstream tooling can adapt deliberately.
