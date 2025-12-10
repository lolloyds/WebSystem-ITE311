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
$routes->get('/course/enrolled', 'Course::getEnrolledCourses');
$routes->get('/course/enrolled/search', 'Course::getEnrolledCourses');
$routes->get('/course/view/(:num)', 'Course::view/$1');

// announcements routes
$routes->get('/announcements', 'Announcement::index');
$routes->post('/announcement/create', 'Announcement::create');

// assignment routes
$routes->get('/assignment/create/(:num)', 'Assignment::createForm/$1');
$routes->post('/assignment/create', 'Assignment::create');
$routes->get('/assignment/courses', 'Assignment::getCourses');
$routes->get('/assignment/view/(:num)', 'Assignment::view/$1');
$routes->get('/assignment/show/(:num)', 'Assignment::show/$1');
$routes->get('/assignment/student/list', 'Assignment::getStudentAssignments');

// role-based dashboard routes (protected by RoleAuth filter)
$routes->group('teacher', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
});

$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
});

// Course admin routes (admin only) - remove filter from group, check in controller
$routes->get('course/admin', 'Course::admin');
$routes->get('course/adminCourses', 'Course::adminCourses');
$routes->get('course/getCourse/(:num)', 'Course::getCourse/$1');
$routes->post('course/update', 'Course::update');
$routes->post('course/create', 'Course::create');

// Course teacher routes (teacher only) - check in controller
$routes->get('course/teacher', 'Course::teacherCourses');
$routes->post('course/teacher/create', 'Course::createTeacherCourse');

// Materials routes
$routes->get('materials/upload/(:num)', 'Materials::upload/$1');
$routes->post('materials/upload/(:num)', 'Materials::upload/$1');
$routes->get('materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('materials/view/(:num)', 'Materials::viewFile/$1');
$routes->get('materials/download/(:num)', 'Materials::download/$1');

// Notifications routes
$routes->get('notifications', 'Notifications::get');
$routes->post('notifications/mark_read/(:num)', 'Notifications::markAsRead/$1');

// Manage Users routes (admin only)
$routes->get('manage-users', 'ManageUsers::index');
$routes->post('manage-users/add', 'ManageUsers::addUser');
$routes->post('manage-users/update-role', 'ManageUsers::updateRole');
$routes->post('manage-users/toggle-status', 'ManageUsers::toggleStatus');
$routes->post('manage-users/change-password', 'ManageUsers::changePassword');
$routes->post('manage-users/edit', 'ManageUsers::editUser');
$routes->get('manage-users/get-teachers', 'ManageUsers::getTeachers');

// Debug route to check users
$routes->get('debug/users', 'Admin::debugUsers');
