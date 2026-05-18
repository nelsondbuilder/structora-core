## Summary

Describe the change.

## Validation

- [ ] `composer dump-autoload -o`
- [ ] `vendor/bin/phpunit`
- [ ] `php scripts/php-syntax-check.php`
- [ ] `php scripts/repository-hygiene.php`
- [ ] `php scripts/security-check.php`
- [ ] `php scripts/validate-cli-json.php`

## Safety Checklist

- [ ] No secrets, cookies, tokens, traces, screenshots, HAR files, logs, `.env` files, `vendor`, or `node_modules`.
- [ ] No automation logic, browser actions, remote fetching, form submission, workflow execution, challenge solving, or bypass behavior.
- [ ] Fixtures are synthetic and public-safe.
- [ ] Tests are included or updated.
- [ ] Docs are updated for public API, schema, CLI, or workflow changes.

## Schema and Compatibility

- [ ] No public schema change.
- [ ] Public schema change is documented and tested.
