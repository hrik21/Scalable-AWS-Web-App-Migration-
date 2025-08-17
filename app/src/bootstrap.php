<?php
/**
 * Bootstrap file for application initialization
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set timezone
date_default_timezone_set('UTC');

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . '=' . trim($value));
    }
}

// Set default environment variables
$defaults = [
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_NAME' => 'aws_php_platform',
    'AWS_REGION' => 'us-east-1'
];

foreach ($defaults as $key => $value) {
    if (!getenv($key)) {
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
    }
}

// Register autoloader for application classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});