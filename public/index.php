<?php

use Core\Router;

require_once '../vendor/autoload.php';

header('Content-Type: application/json');

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$router = new Router();
require_once '../routes/web.php';

$method = $_SERVER['REQUEST_METHOD'];
$route  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$scriptBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if ($scriptBase !== '' && $scriptBase !== '/' && strpos($route, $scriptBase) === 0) {
    $route = substr($route, strlen($scriptBase));
}

$route = $route ?: '/';
$route = preg_replace('#^/index\.php(?:/|$)#', '/', $route);
$route = '/' . ltrim($route, '/');

$routeInfo = $router->match($method, $route);

if ($routeInfo === null) {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'path' => $route]);
    exit;
}

$routeParts = explode('@', $routeInfo['action'], 2);
if (count($routeParts) !== 2) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid route configuration']);
    exit;
}

[$controller, $action] = $routeParts;
$params = $routeInfo['params'];

if (!class_exists($controller)) {
    http_response_code(500);
    echo json_encode(['error' => 'Controller not found']);
    exit;
}

$controllerInstance = new $controller();
if (!method_exists($controllerInstance, $action)) {
    http_response_code(500);
    echo json_encode(['error' => 'Action not found']);
    exit;
}

$request  = new \Core\Request();
$response = $controllerInstance->$action($request, ...$params);

echo json_encode($response);
