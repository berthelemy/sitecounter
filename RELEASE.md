# SiteCounter Release Process

This document defines the release workflow for open source publication.

## Versioning

SiteCounter follows Semantic Versioning:

- MAJOR: breaking changes
- MINOR: backward-compatible features
- PATCH: backward-compatible fixes

## Branch Flow

- main: stable releases only
- develop: upcoming release integration
- feature/* and fix/*: merged into develop
- release/vX.Y.Z: final release branch from develop
- hotfix/vX.Y.Z: urgent fixes from main, merged back to main and develop

## Release Checklist

1. Ensure Phase checklist items are complete in IMPLEMENTATION-TODO.md.
2. Run full tests:

   composer test

3. Confirm manual checks in tests/MANUAL-ACCEPTANCE.md are complete.
4. Update README and docs for any new behavior.
5. Confirm .env and local runtime artifacts are not staged.
6. Merge release branch into main.
7. Create annotated tag.
8. Push main and tag.
9. Create GitHub release notes for the tag.

## Tagging Commands

Example for v1.0.0:

git checkout main
git pull origin main
git tag -a v1.0.0 -m "SiteCounter v1.0.0"
git push origin main
git push origin v1.0.0

## Post-Release

1. Merge main back into develop.
2. Increment version planning notes for next cycle.
3. Track any regressions as issues.
