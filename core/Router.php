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

    //match the route
    public function match($method, $route){
        $method = strtoupper($method);
        $route = '/' . ltrim($route, '/');

        if(isset($this->routes[$method][$route])){
            return $this->routes[$method][$route];
        }
        return null;
    }
        
}
