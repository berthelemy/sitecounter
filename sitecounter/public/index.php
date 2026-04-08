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

$configuredPaths = getenv('SITECOUNTER_PATHS');
if (is_string($configuredPaths) && $configuredPaths !== '' && is_file($configuredPaths)) {
    $pathsConfig = $configuredPaths;
}

if ($pathsConfig === null) {
    $candidates = [
        FCPATH . '../app/Config/Paths.php',
        FCPATH . '../sitecounter/app/Config/Paths.php',
        dirname(FCPATH, 2) . '/sitecounter/app/Config/Paths.php',
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
