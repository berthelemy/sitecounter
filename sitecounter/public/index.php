<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// Fallback for systems without ext-intl
if (!class_exists('Locale')) {
    class Locale
    {
        public static function getDefault(): string
        {
            $locale = ini_get('intl.default_locale');
            return $locale !== false && $locale !== '' ? $locale : 'en_US';
        }

        public static function setDefault(string $locale): string
        {
            ini_set('intl.default_locale', $locale);
            return $locale;
        }
    }
}

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
$pathsConfig = null;

// Read SITECOUNTER_PATHS from .env before the framework bootstraps.
// This lets you set the path in sitecounter/.env without editing version-controlled files.
// We look for .env at the same base locations used for the Paths.php candidates below.
if (empty(getenv('SITECOUNTER_PATHS'))) {
    $dotEnvCandidates = [
        FCPATH . '../.env',
        FCPATH . '../sitecounter/.env',
        dirname(FCPATH, 2) . '/sitecounter/.env',
    ];

    foreach ($dotEnvCandidates as $dotEnvFile) {
        if (! is_file($dotEnvFile)) {
            continue;
        }

        $dotEnvLines = file($dotEnvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($dotEnvLines === false) {
            continue;
        }

        foreach ($dotEnvLines as $dotEnvLine) {
            $dotEnvLine = trim($dotEnvLine);

            // Skip comments and blank lines.
            if ($dotEnvLine === '' || $dotEnvLine[0] === '#') {
                continue;
            }

            if (preg_match('/^SITECOUNTER_PATHS\s*=\s*(.+)$/', $dotEnvLine, $dotEnvMatch)) {
                $dotEnvValue = trim($dotEnvMatch[1]);

                // Strip surrounding single or double quotes.
                if (strlen($dotEnvValue) >= 2
                    && (($dotEnvValue[0] === '"'  && $dotEnvValue[-1] === '"')
                        || ($dotEnvValue[0] === "'" && $dotEnvValue[-1] === "'"))) {
                    $dotEnvValue = substr($dotEnvValue, 1, -1);
                }

                if ($dotEnvValue !== '' && is_file($dotEnvValue)) {
                    putenv('SITECOUNTER_PATHS=' . $dotEnvValue);
                }

                break 2;
            }
        }
    }
}

$configuredPaths = getenv('SITECOUNTER_PATHS');
if (is_string($configuredPaths) && $configuredPaths !== '' && is_file($configuredPaths)) {
    $pathsConfig = $configuredPaths;
}

if ($pathsConfig === null) {
    // Auto-detection: SiteCounter tries the three most common folder layouts.
    // FCPATH is the absolute path to this file's directory (your web root / public_html/).
    //
    // Candidate 1: sitecounter/app/ sits one level above the web root
    //   Web root:  /home/user/public_html/
    //   App root:  /home/user/app/              (non-standard; folder renamed from sitecounter/)
    //
    // Candidate 2: sitecounter/ folder sits one level above the web root (standard shared-hosting layout)
    //   Web root:  /home/user/public_html/
    //   App root:  /home/user/sitecounter/app/
    //
    // Candidate 3: sitecounter/ sits two levels above the web root
    //   Web root:  /home/user/domains/example.com/public_html/
    //   App root:  /home/user/sitecounter/app/
    //
    // If none of these match your layout, either:
    //   a) Set the SITECOUNTER_PATHS environment variable (see README), or
    //   b) Add your own path to the $candidates array below.
    $candidates = [
        FCPATH . '../app/Config/Paths.php',           // Candidate 1 (see above)
        FCPATH . '../sitecounter/app/Config/Paths.php', // Candidate 2 (standard)
        dirname(FCPATH, 2) . '/sitecounter/app/Config/Paths.php', // Candidate 3
    ];

    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            $pathsConfig = $candidate;
            break;
        }
    }
}

if ($pathsConfig === null) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    echo 'Bootstrap error: unable to locate app/Config/Paths.php. '
        . 'Set SITECOUNTER_PATHS or update public/index.php path mapping.';
    exit(1);
}

require $pathsConfig;

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
