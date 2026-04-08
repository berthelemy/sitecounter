# SiteCounter

SiteCounter is a self-hosted website analytics tracker built with CodeIgniter 4 and Bootstrap 5.
It lets an administrator register websites, install a small tracking snippet, and review visit and unique visitor trends.

## Features

- One-time web installer (SQLite-first for initial public release)
- Authentication with password and magic-link reset flow
- Language support with English and French packs
- Website CRUD with per-site tracking token and copy-ready script
- Tracking endpoint with visitor cookie identifier and CORS handling
- Reporting dashboard with unique visitors, total visits, top pages, bottom pages, and timeline chart

## Tech Stack

- PHP 8.2+
- CodeIgniter 4
- CodeIgniter Shield
- Bootstrap 5 + Bootstrap Icons
- SQLite (default and currently supported installer target)

## Quick Start (Local)

1. Install dependencies:

	cd sitecounter && composer install

2. Create local environment file:

	cp env .env

3. Start development server:

	php spark serve

4. Open installer:

	http://localhost:8080/install

## Shared Hosting Deployment

Use the standard secure layout:

- Keep application files outside web root.
- Copy the contents of sitecounter/public/ into public_html/.
- Keep sitecounter/app/, sitecounter/writable/, sitecounter/vendor/, and other project files outside public_html/.
- Adjust paths in public_html/index.php so framework and app paths resolve correctly.
- Set production values in .env, including app.baseURL and SQLite database path.

## Configuration Notes

- Copy sitecounter/env to sitecounter/.env and set production options before go-live.
- Ensure sitecounter/writable/, sitecounter/writable/cache/, sitecounter/writable/logs/, sitecounter/writable/session/, and sitecounter/writable/uploads/ are writable by the web server user.
- For production, set:

  CI_ENVIRONMENT = production

## Running Tests

Run all tests:

cd sitecounter && composer test

or:

cd sitecounter && vendor/bin/phpunit

Coverage reports require Xdebug coverage mode.

## Security

- Installer is one-time and should be inaccessible after successful installation.
- Password minimum length is 8 characters.
- Do not commit .env, local SQLite databases, or writable runtime content.

## Open Source Process

- Contribution process: see CONTRIBUTING.md.
- Release process and tagging: see RELEASE.md.
- Manual acceptance checklist: see sitecounter/tests/MANUAL-ACCEPTANCE.md.

## License

This project is licensed under the GNU General Public License v3.0. See LICENSE.
