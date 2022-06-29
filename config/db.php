<?php
/**
 * Craft 3 Multi-Environment
 * Efficient and flexible multi-environment config for Craft 3 CMS
 *
 * $_ENV constants are loaded by craft3-multi-environment from .env.php via
 * ./web/index.php for web requests, and ./craft for console requests
 */

/**
 * Database Configuration
 *
 * All of your system's database configuration settings go in here.
 * You can see a list of the default settings in src/config/DbConfig.php
 */


return [
    'dsn' => getenv('DB_DSN'),
    'user' => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
    'schema' => getenv('DB_TABLE_SCHEMA'),
    'tablePrefix' => getenv('DB_TABLE_PREFIX'),
];
