<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);

$routes->group('admin', ['filter' => 'session'], static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index', ['filter' => 'permission:admin.access']);
    // Users
    $routes->get('users', 'Admin\Users::index',  ['filter' => 'permission:users.view']);
    $routes->match(['GET','POST'], 'users/create', 'Admin\Users::create', ['filter' => 'permission:users.create']);
    $routes->match(['GET','POST'], 'users/(:num)/edit', 'Admin\Users::edit/$1', ['filter' => 'permission:users.edit']);
    $routes->post('users/(:num)/delete', 'Admin\Users::delete/$1', ['filter' => 'permission:users.delete']);
    $routes->post('users/(:num)/toggle', 'Admin\Users::toggle/$1', ['filter' => 'permission:users.edit']);
});

