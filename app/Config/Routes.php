<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);

$routes->group('admin', ['filter' => 'session'], static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index', ['filter' => 'permission:admin.access']);
    $routes->get('users', 'Admin\Users::index', ['filter' => 'permission:users.view']);
    $routes->match(['get','post'], 'users/create', 'Admin\Users::create', ['filter' => 'permission:users.create']);
});

