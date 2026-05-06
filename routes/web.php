<?php
// Define routes
$router->add('GET', '/', 'App\\Controllers\\HomeController@index');
$router->add('POST', '/login', 'App\\Controllers\\FormController@Login');