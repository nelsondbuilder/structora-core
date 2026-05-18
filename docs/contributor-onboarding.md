# Contributor Onboarding

Welcome to Structora Core.

Start here:

- Read `README.md`.
- Read `docs/project-philosophy.md`.
- Run the test suite.
- Review `docs/result-schema.md`.
- Use synthetic fixtures only.

Local validation:

```bash
composer dump-autoload -o
vendor/bin/phpunit
php scripts/php-syntax-check.php
php scripts/repository-hygiene.php
php scripts/security-check.php
php scripts/validate-cli-json.php
```
