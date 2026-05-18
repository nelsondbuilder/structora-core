# Release Guide

Structora Core release target: `v0.1.0-alpha`.

## Release Checklist

- [ ] Confirm `ReleaseMetadata::VERSION` is `0.1.0-alpha`.
- [ ] Confirm `ReleaseMetadata::TAG` is `v0.1.0-alpha`.
- [ ] Confirm `DiscoveryResult::SCHEMA_VERSION` matches release schema policy.
- [ ] Run `composer dump-autoload -o`.
- [ ] Run `vendor/bin/phpunit`.
- [ ] Run `php scripts/php-syntax-check.php`.
- [ ] Run `php scripts/repository-hygiene.php`.
- [ ] Run `php scripts/security-check.php`.
- [ ] Run `php scripts/validate-cli-json.php`.
- [ ] Review README, CHANGELOG, SECURITY, SUPPORT, and release docs.
- [ ] Confirm no runtime artifacts or private data are committed.

## Tagging Strategy

Use annotated tags:

```bash
git tag -a v0.1.0-alpha -m "Release v0.1.0-alpha"
git push origin v0.1.0-alpha
```

## Semantic Versioning

- Alpha: public experimentation, documented schema, APIs may evolve.
- Beta: stronger compatibility expectations, fewer schema changes.
- Stable: SemVer compatibility and migration guidance for breaking changes.

## Release Workflow

1. Finalize release notes in `CHANGELOG.md`.
2. Run the full validation suite.
3. Create the release tag.
4. Publish GitHub release notes from the changelog.
5. Monitor issues for schema, CLI, and integration feedback.
