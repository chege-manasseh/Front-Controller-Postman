<?php
// Define routes
$router->add('GET', '/', 'App\\Controllers\\HomeController@index');
$router->add('POST', '/login', 'App\\Controllers\\HomeController@Login');
$router->add('POST', '/submit', 'App\\Controllers\\FormController@submit');