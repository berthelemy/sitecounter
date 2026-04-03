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
- [x] Add language preference setting to profile
- [x] Update profile form with new fields
- [x] Manual check: password change works
- [x] Manual check: password reset email sent
- [x] Manual check: language preference saved

## Phase 7: Database Configuration Options
- [ ] Add database type selection to installer
- [ ] Add MySQL/MariaDB configuration fields to installer
- [ ] Update database config based on installer choices
- [ ] Test MySQL/MariaDB connection during install
- [ ] Manual check: MySQL installation works
- [ ] Manual check: MariaDB installation works

## Phase 8: UI Enhancements
- [ ] Add Bootstrap Icons library
- [ ] Update UI to use proper icons throughout
- [ ] Improve visual design and user experience
- [ ] Manual check: icons display correctly
- [ ] Manual check: UI is polished and professional
- [ ] Manual check: chart displays correct daily values

## Phase 6: Testing + Version + Open Source
- [ ] Write PHPUnit unit tests for models and validation
- [ ] Write integration tests for controller routes
- [ ] Document manual acceptance testing cases
- [ ] Implement Git workflow and branch strategy
- [ ] Add README, LICENSE, CONTRIBUTING
- [ ] Add .gitignore for PHP/CI and secrets
- [ ] Run all tests: `vendor/bin/phpunit`
- [ ] Tag release using Git and push tags
- [ ] Manual check: all tests pass
- [ ] Manual check: release tag exists in remote

---

## Notes
- Follow PSR-4 and PHPDoc standards
- Keep installer one-time use only
- Use SQLite default, allow MySQL/MariaDB by `.env` switch
- Keep this task list updated as work progresses
