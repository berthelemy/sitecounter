---
name: codeigniter-development
description: Use when: setting up a new CodeIgniter project or following development best practices. Provides a quick checklist for installation, configuration, and MVC structure.
---

# CodeIgniter Development Skill

This skill guides you through setting up a new CodeIgniter project and adhering to best practices.

## Quick Checklist

1. **Install CodeIgniter**
   - Use Composer: `composer create-project codeigniter4/appstarter project-name`
   - Or download from the official website

2. **Configure Environment**
   - Copy `env` to `.env` in the project root
   - Set `app.baseURL`, database credentials, and other environment variables

3. **Set Up Database**
   - Create a database in your MySQL/PostgreSQL/etc. (default to SQLite for simplicity)
   - Run migrations if available: `php spark migrate`

4. **Follow MVC Structure**
   - Place controllers in `app/Controllers/`
   - Models in `app/Models/`
   - Views in `app/Views/`
   - Routes in `app/Config/Routes.php`

5. **Integrate Front-End with Bootstrap**
   - Follow the bootstrap-frontend skill for setting up Bootstrap 5
   - Add Bootstrap CSS/JS to views (e.g., in `app/Views/layout.php`)
   - Use Bootstrap components in templates

6. **Implement User Management**
   - Set up authentication (use Shield or custom)
   - Create admin user with username/password (min 8 chars)
   - Implement login/logout functionality
   - Allow users to change preferred language

7. **Add Multi-Language Support**
   - Create language files in `app/Languages/` (e.g., `en/` for English)
   - Use `lang()` helper in views and controllers
   - Allow users to switch languages via session/config

8. **Best Practices**
   - Use CodeIgniter's built-in security features (CSRF, XSS protection)
   - Validate user inputs with validation library
   - Use prepared statements or Query Builder for database queries
   - Follow PSR-4 autoloading and naming conventions
   - Use entities for data representation

9. **Test the Setup**
   - Start the development server: `php spark serve`
   - Access the app at `http://localhost:8080`
   - Verify login, Bootstrap styling, and language switching work

This checklist is for CodeIgniter 4. Adjust for version 3 if needed.