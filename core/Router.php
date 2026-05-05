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
        $this->routes[$method][$route]=$action;
    }

    //match the route
    public function match($method, $route){
        if(isset($this->routes[$method][$route])){
            return $this->routes[$method][$route];
        }
        return "404 Not Found";
    }
        
}
