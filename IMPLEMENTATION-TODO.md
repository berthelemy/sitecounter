# SiteCounter Implementation Task List

This file tracks progress for all implementation phases with manual verification checks.

## Phase 1: Project Setup + Installer
- [x] Initialize CodeIgniter 4 project (`composer create-project codeigniter4/appstarter sitecounter`)
- [x] Confirm app directories: `app/`, `public/`, `system/`
- [x] Configure `.env` (baseURL, DB config, app.env=development)
- [x] Add CORS support and tracking endpoint filters
- [x] Add Bootstrap includes to layout
- [x] Create installer controller and one-time run protection
- [x] Manual check: CI welcome page loads at `http://localhost:8080`
- [x] Manual check: installer works pre-install, fails after install
- [x] Manual check: Bootstrap styling applies for buttons

## Phase 2: Auth + User + Language
- [x] Add authentication logic (Shield or custom)
- [x] Create `UserModel` with firstname, lastname, email, password
- [x] Seed admin user via migration
- [x] Implement login/logout and profile routes / views
- [x] Validate password minimum 8 chars
- [x] Add multi-language files in `app/Languages/`
- [x] Add language switch UI and persistence
- [x] Manual check: admin can login and logout
- [x] Manual check: weak password is rejected
- [x] Manual check: language switching changes UI

## Phase 3: Website CRUD + Tracking Script
- [x] Create `websites` table migration
- [x] Implement CRUD controller/views for websites
- [x] Generate tracking script per website with token
- [x] Add clipboard copy button to website detail
- [x] Manual check: website add/edit/delete works
- [x] Manual check: snippet copy button returns script text

## Phase 4: Tracking endpoint + Visitor ID cookie
- [x] Create tracking endpoint route and controller
- [x] Implement JS snippet to set cookie + post data
- [x] Add visitor ID cookie generation (UUID)
- [x] Ensure CORS response headers are set properly
- [x] Create `visits` table with needed fields
- [x] Manual check: request from external script stores visit
- [x] Manual check: same browser cookie keeps visitor ID

## Phase 5: Reporting Dashboard
- [x] Build query methods for unique visitor/day/month/year
- [x] Build top 10 and bottom 10 page view queries
- [x] Build daily page views timeline dataset
- [x] Build report views with Bootstrap and Chart.js
- [x] Manual check: report stats match captured visits

## Phase 6: User Profile Management
- [x] Add password change functionality to profile page
- [x] Add password reset request from login screen
- [x] Add language preference switching and persistence
- [x] Update profile form with new fields
- [x] Allow magic-link (forgot password) users to set a new password without current password (one-time reset mode)
- [x] Harden profile update normalization (trim/lowercase email and case-insensitive uniqueness checks)
- [x] Manual check: password change works
- [x] Manual check: password reset email sent
- [x] Manual check: language switch persists across pages

## Phase 7: Database Configuration Options
- [x] Keep installer focused on SQLite for initial release
- [x] Remove external SQL configuration fields from installer
- [x] Update database config to SQLite-only defaults
- [x] Test SQLite connection during install
- [x] Manual check: SQLite installation works

## Phase 8: UI Enhancements
- [x] Add Bootstrap Icons library
- [x] Update UI to use proper icons throughout
- [x] Improve visual design and user experience
- [x] Manual check: icons display correctly
- [x] Manual check: UI is polished and professional
- [x] Manual check: chart displays correct daily values

## Phase 9: Testing + Version + Open Source
- [x] Write PHPUnit unit tests for models and validation
- [x] Write integration tests for password reset controller flow (magic-link reset mode + normal password change requirement)
- [x] Document manual acceptance testing cases
- [x] Implement Git workflow and branch strategy
- [x] Add README, LICENSE, CONTRIBUTING
- [x] Add .gitignore for PHP/CI and secrets
- [x] Run all tests: `vendor/bin/phpunit` (run with `XDEBUG_MODE=coverage`; tests pass)
- [ ] Tag release using Git and push tags
- [x] Manual check: all tests pass
- [ ] Manual check: release tag exists in remote

## Phase 10: Tidy up
- [ ] Improve the vertical alignment of the navbar text "Welcome" so it aligns with everything else in the navbar
- [ ] Ensure that the main menu remains the same for all pages.
- [ ] On the login page, ensure that the language selector has sufficient contrast with the background

## Phase 11: Dashboard improvements
- [ ] On the dashboard, add a card for each tracked website showing the total number of unique visitors
- [ ] On the dashboard, within the card for each tracked website show the average number of unique visitors per month
- [ ] On the dashboard, within the card for each tracked website show the number of unique visitors last month
- [ ] On the dashboard, within the card for each tracked website show the total number of visits
- [ ] On the dashboard, within the card for each tracked website show the average number of visits per month
- [ ] On the dashboard, within the card for each tracked website show the number of visits last month
- [ ] On the reports page for each website show the total number of unique visitors
- [ ] On the reports page for each website show the average number of unique visitors per month
- [ ] On the reports page for each website show the number of unique visitors last month
- [ ] On the reports page for each website show the total number of visits
- [ ] On the reports page for each website show the average number of visits per month
- [ ] On the reports page for each website show the number of visits last month

## Session Handoff (2026-04-04)

Completed in this session:
- Added French override for Shield Auth message `invalidEmail` so "Unable to verify the email address" is translated.
- Implemented profile update hardening in `Dashboard::updateProfile`:
	- trims profile inputs
	- lowercases email before storage
	- checks email uniqueness case-insensitively
	- normalizes repeated spaces in firstname/lastname
- Implemented forgot-password UX behavior in `Dashboard` + profile view:
	- detects magic-link login tempdata
	- enables one-time `password_reset_mode`
	- allows password change without current password while in reset mode
	- clears reset mode after successful password change
- Added automated integration tests in `tests/session/PasswordResetFlowTest.php` covering:
	- magic-link login enables reset mode
	- reset mode allows new password without current password
	- normal flow still requires current password

Next recommended work:
- Phase 7 (updated): keep installer SQLite-only for initial public release.
- Phase 9 (pending):
	- add integration tests for profile email normalization and uniqueness edge cases
	- run full test suite and resolve any failures
	- document manual acceptance checks for auth/profile/report flows

## Session Continuation (2026-04-05)

Completed in this session:
- Implemented Phase 7 installer database options:
	- installer now supports SQLite-only installation flow
	- installer captures SQLite database file path
	- installer validates SQLite path and tests DB connectivity before migration
	- installer persists selected DB config into `.env` and applies runtime settings before migrations
- Added localized installer copy for English and French (`install` language keys + install validation messages).
- Added integration tests for profile update edge cases in `tests/session/ProfileUpdateFlowTest.php`:
	- profile name/email normalization on update
	- case-insensitive duplicate email rejection behavior
- Fixed profile email uniqueness logic for cross-driver compatibility in `Dashboard::updateProfile`.
- Added manual acceptance checklist in `tests/MANUAL-ACCEPTANCE.md`.
- Ran full test suite (`vendor/bin/phpunit`): tests pass; PHPUnit exits with warning due to missing coverage mode.

Next recommended work:
- Phase 7 manual checks:
	- verify end-to-end installer success with SQLite on fresh state
- Phase 9 pending:
	- add model/validation unit tests
	- complete release process tasks (branch strategy, release tagging)


---

## Notes
- Follow PSR-4 and PHPDoc standards
- Keep installer one-time use only
- Use SQLite only for initial public release
- Keep this task list updated as work progresses
