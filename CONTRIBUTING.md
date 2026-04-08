# Contributing to SiteCounter

Thank you for contributing.

## Development Setup

1. Install dependencies (from within the sitecounter directory):

   composer install

2. Create local environment file:

   cp sitecounter/env sitecounter/.env

3. Run locally:

   php spark serve

4. Run tests before opening a pull request:

   composer test

## Branch Strategy

- main: production-ready release history
- develop: integration branch for upcoming release work
- feature/<short-topic>: new features and enhancements
- fix/<short-topic>: bug fixes
- release/vX.Y.Z: release hardening and final docs
- hotfix/vX.Y.Z: emergency fixes on top of main

## Commit Guidelines

- Use clear, imperative commit messages.
- Keep commits focused and small.
- Reference issue numbers when applicable.

Recommended format:

<type>: <short summary>

Examples:

feat: add website report monthly averages
fix: normalize profile email before save
chore: update manual acceptance checklist

## Pull Request Checklist

- The branch is up to date with target branch.
- Tests pass locally.
- New behavior includes tests when practical.
- Documentation is updated when behavior/config changed.
- No secrets, runtime files, or local databases are committed.

## Coding Standards

- Follow PSR-4 autoloading and project coding style.
- Validate all user input and escape output in views.
- Prefer framework services/helpers over custom reimplementation.
- Keep installer one-time and SQLite-focused for initial public release.

## Security Reporting

Do not open public issues for sensitive vulnerabilities.
Instead, contact the maintainers privately through repository security channels once configured.
