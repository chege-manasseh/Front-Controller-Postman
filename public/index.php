<?php
use Core\Router;
require_once '../vendor/autoload.php';

$router = new Router();

//load routes
require_once '../routes/web.php';

// Match the current request
$method = $_SERVER['REQUEST_METHOD'];
$route = $_SERVER['REQUEST_URI'];
$routeInfo = $router->match($method, $route);

if ($routeInfo === "404 Not Found") {
    echo $routeInfo;
} else {
    list($controller, $action) = explode('@', $routeInfo);
    // Call the controller action (this is a simplified example)
    (new $controller())->$action();
}
