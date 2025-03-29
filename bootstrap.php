<?php
// Define constants
define("DS", DIRECTORY_SEPARATOR);
define("ROOT_PATH", __DIR__ . DS);

// Load environment variables
if (file_exists(ROOT_PATH . '.env')) {
    $envVars = parse_ini_file(ROOT_PATH . '.env');
    foreach ($envVars as $key => $value) {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// Register autoloader
require_once ROOT_PATH . 'App' . DS . 'Helper' . DS . 'Autoloader.php'; 