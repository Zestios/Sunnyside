<?php
/**
 * Craft 3 Multi-Environment
 * Efficient and flexible multi-environment config for Craft 3 CMS
 *
 * $_ENV constants are loaded by craft3-multi-environment from .env.php via
 * ./web/index.php for web requests, and ./craft for console requests
 */

/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here.
 * You can see a list of the default settings in src/config/GeneralConfig.php
 */

use craft\helpers\App;

// Determine the incoming protocol
if (isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
    || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0
) {
    $protocol = "https://";
} else {
    $protocol = "http://";
}
// Determine the server hostname
$httpHost = $_SERVER['HTTP_HOST'] ?? '';

$isDev = App::env('ENVIRONMENT') === 'local';
$isProd = App::env('ENVIRONMENT') === 'production';

return [

    // All environments
    '*' => [
        // Craft defined config settings
        'cacheDuration' => false,
        'defaultSearchTermOptions' => array(
            'subLeft' => true,
            'subRight' => true,
        ),
        'enableCsrfProtection' => true,
        'generateTransformsBeforePageLoad' => true,
        'omitScriptNameInUrls' => true,
        'securityKey' => getenv('SECURITY_KEY'),
        'useEmailAsUsername' => false,
        'usePathInfo' => true,
        'extraAllowedFileExtensions' => 'json',
        'verificationCodeDuration' => 'P1M',
        'postLogoutRedirect' => '/',
        'cpTrigger' => App::env('CP_TRIGGER') ?: 'craftcms',
        'devMode' => $isDev,
        'allowUpdates' => $isDev,
        'allowAdminChanges' => $isDev,
        'disallowRobots' => !$isProd,
        'enableTemplateCaching' => $isProd,
        // Aliases parsed in sites’ settings, volumes’ settings, and Local volumes’ settings
        'aliases' => [
            '@basePath' => realpath(dirname(__FILE__)) . '/../public/',
            '@webroot' => realpath(dirname(__FILE__)) . '/../public/',
            '@baseUrl' => $protocol . $_SERVER['HTTP_HOST'] ?? '',
        ],
        // Custom site-specific config settings
        'custom' => [
            'craftEnv' => CRAFT_ENVIRONMENT,
            'codeVersion' => App::env('CODE_VERSION'),
            // 'staticAssetsVersion' => App::env('STATIC_ASSETS_VERSION'),
            'staticAssetsVersion' => time(),
        ],
        'apiKeys' => [
            'googleMaps' => getenv('GOOGLE_API_KEY'),
            'hotjar' => getenv('HOTJAR_KEY'),
            'bugherd' => getenv('BUGHERD_KEY'),
        ],
    ],

    // Live (production) environment
    'production' => [],

    // Staging (pre-production) environment
    'staging' => [],

    // Local (development) environment
    'local' => [],
];
