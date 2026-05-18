# Security Policy

## Supported Versions

Security support begins with the first tagged public release.

## Reporting a Vulnerability

Please report vulnerabilities privately through GitHub Security Advisories or the published project security contact.

Do not disclose vulnerabilities publicly until maintainers have reviewed and coordinated a response.

## Scope

Reports may include:

- Unsafe file handling
- Secret leakage risk
- Unsafe renderer behavior
- Dependency vulnerabilities
- SSRF or unsafe URL handling
- Result serialization issues

## Out of Scope

Structora Core does not support or accept reports related to building checkout automation, captcha solving, credentialed abuse, payment submission, or bypass tooling.

## Safety Principle

Structora Core is read-only by design. It analyzes structure and produces discovery output. It does not submit forms, perform transactions, authenticate sessions, bypass access controls, or automate user actions.
