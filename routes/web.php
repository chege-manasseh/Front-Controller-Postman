<?php

$router->add('GET',  '/users',        'App\\Controllers\\UsersController@index');
$router->add('GET',  '/users/{id}',    'App\\Controllers\\UsersController@show');
$router->add('POST', '/users',         'App\\Controllers\\UsersController@store');
$router->add('POST', '/users/import',  'App\\Controllers\\UsersController@import');