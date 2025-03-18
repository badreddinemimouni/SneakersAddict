<?php
// fct pr charger les variables d'environnement
function loadEnv() {
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
}

// charger les variables d'environnement
loadEnv();

// fct pour obtenir une variable d'environnement
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// config de la base de données
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'users'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASSWORD', env('DB_PASSWORD', 'root'));
define('DB_PORT', env('DB_PORT', '3306'));

// config de l'application
define('APP_NAME', env('APP_NAME', 'SneakersAddict'));
define('APP_URL', env('APP_URL', 'http://localhost/tests/SAE-23_LUU_MIMOUNI/tests'));
define('APP_DEBUG', env('APP_DEBUG', 'true') === 'true'); 