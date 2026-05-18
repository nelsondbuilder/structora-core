# Release Process

Release target: `v0.1.0-alpha`.

## Before Tagging

```bash
composer dump-autoload -o
vendor/bin/phpunit
php scripts/php-syntax-check.php
php scripts/repository-hygiene.php
php scripts/security-check.php
php scripts/validate-cli-json.php
```

## Tag

```bash
git tag -a v0.1.0-alpha -m "Release v0.1.0-alpha"
git push origin v0.1.0-alpha
```

## After Tagging

- Publish GitHub release notes.
- Include safety model and scope boundaries.
- Invite alpha feedback on schema, CLI, exporters, and extensions.
