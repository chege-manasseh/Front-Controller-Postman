<?php
// Define routes
$router->add('GET', '/', 'App\\Controllers\\HomeController@index');
$router->add('POST', '/login', 'App\\Controllers\\FormController@Login');
$router->add('GET', '/user/{id}', 'App\\Controllers\\UsersController@show');
$router->add('GET', '/users', 'App\\Controllers\\UsersController@index');