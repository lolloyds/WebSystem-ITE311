<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// routes for login register and dashboard
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

// settings routes
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/updateProfile', 'Settings::updateProfile');
$routes->post('/settings/changePassword', 'Settings::changePassword');

// course routes
$routes->get('/courses', 'Course::index');
$routes->get('/courses/search', 'Course::search');
$routes->post('/courses/search', 'Course::search');
$routes->post('/course/enroll', 'Course::enroll');
$routes->get('/course/view/(:num)', 'Course::view/$1');

// announcements route
$routes->get('/announcements', 'Announcement::index');

// role-based dashboard routes (protected by RoleAuth filter)
$routes->group('teacher', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
});

$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
});

// Materials routes
$routes->get('materials/upload/(:num)', 'Materials::upload/$1');
$routes->post('materials/upload/(:num)', 'Materials::upload/$1');
$routes->get('materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('materials/view/(:num)', 'Materials::viewFile/$1');
$routes->get('materials/download/(:num)', 'Materials::download/$1');

// Notifications routes
$routes->get('notifications', 'Notifications::get');
$routes->post('notifications/mark_read/(:num)', 'Notifications::markAsRead/$1');
