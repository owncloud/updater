# agents.md — updater

## Repository Overview

A web-based upgrade tool for ownCloud Server that automates backups, package extraction and file replacement during version upgrades.

- **Classification:** Classic (OC10)
- **Activity Status:** Active
- **License:** AGPL-3.0 (license file is `COPYING-AGPL`)
- **Language:** PHP

## Architecture & Key Paths

- `app/` — Core application logic
- `src/` — Source code
- `pub/` — Public-facing web assets
- `index.php` — Entry point
- `application.php` — Application bootstrap
- `tests/` — PHPUnit test suites
- `Makefile` — Build and test orchestration
- `composer.json` — PHP dependency management
- `phpstan.neon` — PHPStan static analysis config
- `phpunit.xml` — PHPUnit configuration
- `CONTRIBUTING.md` — Contribution guidelines
- `COPYING-AGPL` — AGPL-3.0 license file
- `CHANGELOG.md` — Version changelog

## Development Conventions

- PHP application with Composer dependency management
- Code style enforced by phpcs
- PHPStan for static analysis
- `CONTRIBUTING.md` present at repo root
- License file is `COPYING-AGPL` (not `LICENSE`)

## Build & Test Commands

```bash
make                        # Install all dependencies (composer + JS)
make clean                  # Clean build artifacts
make test-php-unit          # Run PHP unit tests (if target available)
make test-php-style         # Check code style
```

## Important Constraints

- **AGPL-3.0 copyleft license:** The OSPO Apache 2.0 migration requires auditing this copyleft license.
- **License file naming:** The license file is `COPYING-AGPL`, not `LICENSE`.
- **Security-sensitive:** Handles file system operations during upgrades -- changes carry risk of data loss.
- **Backup integrity:** The updater creates backups before modifying the installation; this workflow must be preserved.


## OSPO Policy Constraints

### GitHub Actions
- **Only** use actions owned by `owncloud`, created by GitHub (`actions/*`), verified on the GitHub Marketplace, or verified by the ownCloud Maintainers.
- Pin all actions to their full commit SHA (not tags): `uses: actions/checkout@<SHA> # vX.Y.Z`
- Never introduce actions from unverified third parties.

### Dependency Management
- Dependabot is configured for automated dependency updates.
- Review and merge Dependabot PRs as part of regular maintenance.
- Do not introduce new dependencies without discussion in an issue first.

### Git Workflow
- **Rebase policy**: Always rebase; never create merge commits. Use `git pull --rebase` and `git rebase` before pushing.
- **Signed commits**: All commits **must** be PGP/GPG signed (`git commit -S -s`).
- **DCO sign-off**: Every commit needs a `Signed-off-by` line (`git commit -s`).
- **Conventional Commits & Squash Merge**: Use the [Conventional Commits](https://www.conventionalcommits.org/) format where the repository enforces it. Many repos use squash merge, where the PR title becomes the commit message on the default branch — apply Conventional Commits format to PR titles as well. A reusable GitHub Actions workflow enforces this.

## Context for AI Agents

- This is a PHP web application that runs within an ownCloud Server installation.
- Entry point is `index.php`; application bootstrap in `application.php`.
- The `app/` and `src/` directories contain the core upgrade logic.
- The updater preserves `data`, `config` and `themes` directories during upgrades.
- The `pub/` directory contains web assets for the upgrade UI.
