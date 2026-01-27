<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Root URL - redirect to login
$routes->get('/', 'Auth::index');

// Authentication routes
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('auth/verify-code', 'Auth::verifyCodePage');
$routes->post('auth/verify-code', 'Auth::verifyCode');
$routes->post('auth/resend-code', 'Auth::resendCode');
$routes->get('auth/logout', 'Auth::logout');

// Dashboard route (for regular admin)
$routes->get('dashboard', 'Dashboard::index');

// Academic Records routes
$routes->get('academic-records', 'AcademicRecords::index');
$routes->post('academic-records/upload', 'AcademicRecords::upload');

// Settings route (for regular admin)
$routes->get('settings', 'Settings::index');

// Super Admin routes
$routes->group('super-admin', function($routes) {
    // Dashboard
    $routes->get('dashboard', 'SuperAdmin::index');
    
    // User Management
    $routes->get('user-management', 'SuperAdmin::userManagement');
    $routes->post('add-admin', 'SuperAdmin::addAdmin');
    $routes->post('edit-admin/(:num)', 'SuperAdmin::editAdmin/$1');
    $routes->post('delete-admin/(:num)', 'SuperAdmin::deleteAdmin/$1');
    $routes->get('delete-admin/(:num)', 'SuperAdmin::deleteAdmin/$1');
    $routes->post('approve-admin/(:num)', 'SuperAdmin::approveAdmin/$1');
    $routes->get('approve-admin/(:num)', 'SuperAdmin::approveAdmin/$1');
    $routes->post('reject-admin/(:num)', 'SuperAdmin::rejectAdmin/$1');
    $routes->get('reject-admin/(:num)', 'SuperAdmin::rejectAdmin/$1');
    $routes->post('toggle-suspend/(:num)', 'SuperAdmin::toggleSuspend/$1');
    $routes->get('toggle-suspend/(:num)', 'SuperAdmin::toggleSuspend/$1');
    $routes->get('get-user/(:num)', 'SuperAdmin::getUserData/$1');
    
    // All Records
    $routes->get('all-records', 'SuperAdmin::allRecords');
    
    // System Backup
    $routes->get('system-backup', 'SuperAdmin::systemBackup');
    
    // Settings
    $routes->get('settings', 'SuperAdmin::settings');
});