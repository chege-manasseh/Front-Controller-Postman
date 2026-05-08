<?php 
namespace Core;
class Router{
    public $routes=[
        'GET'=>[],
        'POST'=>[],
        'PUT'=>[],
        'DELETE'=>[]
    ];
    
    //routing table
    public function add($method, $route, $action){
        $method = strtoupper($method);
        $route = '/' . ltrim($route, '/');

        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][$route]=$action;
    }

    //match the route — supports static routes and {param} placeholders
    public function match($method, $route){
        $method = strtoupper($method);
        $route = '/' . ltrim($route, '/');

        // 1. Exact static match (fast path)
        if(isset($this->routes[$method][$route])){
            return ['action' => $this->routes[$method][$route], 'params' => []];
        }

        // 2. Dynamic match: convert {param} patterns to named regex groups
        foreach ($this->routes[$method] ?? [] as $pattern => $action) {
            $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';
            if (preg_match($regex, $route, $matches)) {
                // Keep only named captures (the param values)
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return ['action' => $action, 'params' => $params];
            }
        }

        return null;
    }
        
}
