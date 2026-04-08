# SiteCounter Manual Acceptance Checklist

Use this checklist for release validation across installer, auth/profile, and reporting flows.

## Test setup

- Start with a fresh application state (uninstalled).
- Confirm writable permissions for app storage paths.

## Installer flow

### SQLite installation (default)

1. Open /install.
2. Keep database type as SQLite.
3. Set SQLite filename (for example: sitecounter.db).
4. Run installation.
5. Verify success message and redirect.
6. Verify /install cannot run again after install.
7. Verify .env database settings were updated for SQLite.

Expected result:
- Installer completes and migrations create required tables.
- Install lockout is enforced after first successful install.

## Authentication and profile

### Login and logout

1. Log in with admin account.
2. Confirm dashboard loads.
3. Log out.
4. Confirm redirect to login screen.

Expected result:
- Authenticated pages require login and session is cleared on logout.

### Password reset via magic link mode

1. Request password reset from login screen.
2. Open the magic link from email.
3. Navigate to profile password form.
4. Set new password without current password.
5. Save and confirm success.
6. Attempt password change again without current password.

Expected result:
- First change works in one-time reset mode.
- Reset mode is cleared after success.
- Current password is required in normal mode.

### Profile normalization and email uniqueness

1. Update profile first and last name with extra internal and surrounding spaces.
2. Update email with uppercase letters and surrounding spaces.
3. Save profile.
4. Verify saved names collapse repeated spaces.
5. Verify saved email is lowercase.
6. Attempt to change to another user email with case-only difference.

Expected result:
- Name spacing is normalized.
- Email is stored normalized to lowercase.
- Case-insensitive duplicate email is rejected.

## Website tracking and reports

### Website CRUD and snippet

1. Add a new website.
2. Edit it.
3. Delete and re-add it.
4. Copy the tracking script using the button.

Expected result:
- CRUD operations succeed.
- Tracking script copy returns expected script text.

### Tracking endpoint and reports

1. Install script on a test page.
2. Load page multiple times from same browser.
3. Load from second browser/session.
4. Open website report.

Expected result:
- Visits are recorded with title, URL, timestamp, and visitor id.
- Same browser reuses cookie visitor id.
- Report cards/tables/chart reflect stored visits.

## Language and UI checks

1. Switch language on login and dashboard pages.
2. Verify translated labels/messages are shown.
3. Verify language preference persists.
4. Verify login language selector contrast is acceptable.
5. Verify navbar welcome alignment.

Expected result:
- Language switching is functional and persistent.
- Key UI alignment/contrast items are acceptable.
