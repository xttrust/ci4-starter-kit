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

    // User Notifications
    $routes->get('notifications/recent', 'Admin\Notifications::recent', ['filter' => 'permission:admin.access']);
    $routes->get('notifications/unread-count', 'Admin\Notifications::unreadCount', ['filter' => 'permission:admin.access']);
    $routes->post('notifications/mark-read', 'Admin\Notifications::markRead', ['filter' => 'permission:admin.access']);
    $routes->post('notifications/mark-all-read', 'Admin\Notifications::markAllReadPerUser', ['filter' => 'permission:notifications.view']);
    // Admin CRUD for notifications
    $routes->get('notifications', 'Admin\Notifications::index', ['filter' => 'permission:notifications.view']);
    $routes->match(['GET','POST'], 'notifications/form/(:num)', 'Admin\Notifications::form/$1', ['filter' => 'permission:notifications.manage']);
    $routes->match(['GET','POST'], 'notifications/form', 'Admin\Notifications::form', ['filter' => 'permission:notifications.manage']);
    $routes->post('notifications/save', 'Admin\Notifications::save', ['filter' => 'permission:notifications.manage']);
    $routes->post('notifications/delete/(:num)', 'Admin\Notifications::delete/$1', ['filter' => 'permission:notifications.manage']);
    $routes->post('notifications/purge', 'Admin\Notifications::purge', ['filter' => 'permission:notifications.manage']);
    $routes->post('notifications/mark-as-read/(:num)', 'Admin\Notifications::markAsRead/$1', ['filter' => 'permission:notifications.manage']);
    $routes->post('notifications/mark-read-selected', 'Admin\Notifications::markReadSelected', ['filter' => 'permission:notifications.manage']);
    
    // Activity / Audit log
    $routes->get('activity', 'Admin\Activity::index', ['filter' => 'permission:audit.view']);
    // Keep old `admin/audit` path working for sidebar links
    $routes->get('audit', 'Admin\Activity::index', ['filter' => 'permission:audit.view']);
    $routes->post('activity/delete/(:num)', 'Admin\Activity::delete/$1', ['filter' => 'permission:audit.*']);
    $routes->post('activity/purge', 'Admin\Activity::purge', ['filter' => 'permission:audit.*']);
});

