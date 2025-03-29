<?php

ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 43200); // 12 heures au lieu de 24
session_set_cookie_params([
    'lifetime' => 43200, // 12 heures
    'domain' => $_SERVER['SERVER_NAME'],
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']), // Active secure=true si HTTPS est utilisé
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Load bootstrap (which includes autoloader and environment variables)
require_once __DIR__ . '/bootstrap.php';

require_once 'App/Config/Init.php';
require_once 'App/Config/Container.php';
require_once 'App/Routes/api.php';

session_start();

// Protection contre la fixation de session
if (!isset($_SESSION['initialized'])) {
    session_regenerate_id(true);
    $_SESSION['initialized'] = true;
}

// Protection CSRF globale
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Vérification CSRF pour les requêtes POST, PUT, DELETE
$request_method = $_SERVER['REQUEST_METHOD'];
if (in_array($request_method, ['POST', 'PUT', 'DELETE']) && 
    !in_array(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ['/login', '/register'])) {
    
    $csrf_token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    
    if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo json_encode(['error' => 'CSRF token validation failed']);
        exit;
    }
}

App\Routes\Router::route($request_method);