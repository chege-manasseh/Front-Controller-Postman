<?php

use Core\Router;
require_once '../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$router = new Router();

// Load routes (must come after $router is instantiated)
require_once '../routes/web.php';

// Match the current request
$method = $_SERVER['REQUEST_METHOD'];
$route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize route when app is accessed via symlink/subdirectory.
$scriptBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if ($scriptBase !== '' && $scriptBase !== '/' && strpos($route, $scriptBase) === 0) {
    $route = substr($route, strlen($scriptBase));
}

if ($route === '' || $route === false) {
    $route = '/';
}

$route = preg_replace('#^/index\.php(?:/|$)#', '/', $route);
$route = '/' . ltrim($route, '/');
$routeInfo = $router->match($method, $route);

if ($routeInfo === null) {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

$routeParts = explode('@', $routeInfo['action'], 2);
if (count($routeParts) !== 2) {
    http_response_code(500);
    echo '500 Invalid route action';
    exit;
}

list($controller, $action) = $routeParts;
$params = $routeInfo['params'];

if (!class_exists($controller)) {
    http_response_code(500);
    echo '500 Controller not found';
    exit;
}

$controllerInstance = new $controller();
if (!method_exists($controllerInstance, $action)) {
    http_response_code(500);
    echo '500 Action not found';
    exit;
}

$request = new \Core\Request();
$response = $controllerInstance->$action($request, ...$params);

if (is_array($response)) {
    header('Content-Type: application/json');
    echo json_encode($response);
}


// Since your index.php is also handling JSON responses at the bottom, does your Request class have a way to detect if the current request is an AJAX/API call versus a standard page load?
