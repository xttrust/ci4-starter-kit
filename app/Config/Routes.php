<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);

$routes->group('admin', [
    'filter' => 'session',
], static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index', ['filter' => 'group:superadmin,admin,developer']);
    // $routes->get('artists', 'Admin\Artists::index', ['filter' => 'permission:admin.access']);
});

