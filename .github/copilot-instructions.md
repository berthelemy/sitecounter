# Copilot Instructions for SiteCounter CodeIgniter Project

## Project Overview
This is a web application for tracking website usage, built with CodeIgniter 4 and Bootstrap 5. It allows administrators to manage websites and track page visits via JavaScript snippets.

## Tech Stack
- Backend: CodeIgniter 4
- Frontend: Bootstrap 5
- Database: SQLite (default), optional MySQL/MariaDB
- Language: PHP, JavaScript
- Testing: PHPUnit for unit/integration, manual acceptance testing

## Development Guidelines
- Follow MVC architecture strictly
- Use built-in CodeIgniter security features (CSRF, validation)
- Implement user authentication with admin role
- Support multi-language (English default, extensible)
- Ensure responsive design with Bootstrap

## Skill Usage
- For front-end setup: Use `/bootstrap-frontend`
- For CodeIgniter development: Use `/codeigniter-development`
- For testing: Use `/testing`
- For open source preparation: Use `/open-source`
- For version control: Use `/version-control`

## Coding Standards
- Use PSR-4 autoloading
- Add PHPDoc comments to all classes and methods
- Validate inputs and escape outputs
- Follow semantic versioning for releases

## Security
- Passwords minimum 8 characters
- No command-line installation after setup
- Remove sensitive data before open sourcing