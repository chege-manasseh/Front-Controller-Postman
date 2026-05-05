<?php

use Core\Router;
use Config\Database;
require_once '../vendor/autoload.php';
require_once "../config/database.php";
//load routes
require_once '../routes/web.php';
require_once "../vendor/autoload.php";

$router = new Router();


// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$db = new Database();
$conn = $db->connect();

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

$routeParts = explode('@', $routeInfo, 2);
if (count($routeParts) !== 2) {
    http_response_code(500);
    echo '500 Invalid route action';
    exit;
}

list($controller, $action) = $routeParts;

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

$controllerInstance->$action();
