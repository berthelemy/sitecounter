---
name: codeigniter-url-resolution
description: Use when: ensuring a CodeIgniter application resolves URLs correctly on local/test and production hosts.
---

# CodeIgniter URL Resolution Skill

This skill explains how to write a CodeIgniter application so URLs resolve correctly across different hosts and environments.

## Key Principles

1. Use the `public` folder as the web server document root.
2. Keep host-specific values out of your views and controllers.
3. Define the base URL through environment configuration.
4. Always include a trailing slash on `app.baseURL`.
5. Use CodeIgniter route and URL helpers instead of hard-coded links.

## Recommended Configuration

### `app/Config/App.php`

- Set a default base URL for local development.
- Override it with `env('app.baseURL')` so production and test hosts can provide their own value.
- Normalize it with `rtrim()` and add a trailing slash.

Example pattern used in this app:

```php
public string $baseURL = 'http://localhost:8080/';

public function __construct()
{
    parent::__construct();

    $configuredBaseURL = rtrim((string) env('app.baseURL', $this->baseURL), '/');
    $this->baseURL = ($configuredBaseURL === '' ? $this->baseURL : $configuredBaseURL) . '/';
}
```

### `.env` and environment variables

- Use the `.env` file in development and test environments.
- Use actual environment variables on production.

Example `.env` values:

```ini
app.baseURL = 'http://localhost:8080/'
```

Production example:

```ini
app.baseURL = 'https://example.com/'
```

If your application runs in a subdirectory:

```ini
app.baseURL = 'https://example.com/subfolder/'
```

## Public web root and rewrite rules

- Ensure the web server points to `public/` as the document root.
- If you cannot point the web root at `public/`, use front-controller bootstrap logic carefully.
- Use `public/.htaccess` to route requests to `index.php` and remove `index.php` from URLs.

If `public/.htaccess` is active, set:

```php
public string $indexPage = '';
```

and keep `uriProtocol` at `REQUEST_URI` for normal server behavior.

## URL helper usage

- Use built-in helpers like `base_url()` and `site_url()` in views and templates.
- Avoid hardcoding hostnames or protocol strings in HTML.

Example:

```php
<a href="<?= base_url('dashboard') ?>">Dashboard</a>
```

## Handling test vs production hosts

- Test host: local or staging servers should set `app.baseURL` to match their HTTP host.
- Production host: set `app.baseURL` to the live domain.
- Keep the same routing logic and controller code across environments.
- Use `allowedHostnames` if the app should accept additional hostnames besides the configured base URL.

## Troubleshooting

- If pages 404, verify `public` is the document root and `.htaccess` rewrite rules are working.
- If `base_url()` generates the wrong host, check the `app.baseURL` environment setting.
- If index.php appears in URLs unexpectedly, ensure `indexPage` is set to an empty string and the server rewrite module is enabled.

## Best practice summary

- Base URL belongs in environment configuration, not hardcoded app logic.
- Normalize the base URL with a trailing slash.
- Keep the `public` directory as the entry point.
- Use helpers and routes for links, not explicit hostnames.
- Support both local/test and production hosts with the same app code.
