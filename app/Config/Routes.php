<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── Auth ──────────────────────────────────────────────────────────────────────
$routes->get('/',                    'Auth::index');
$routes->get('login',                'Auth::index');
$routes->post('login',               'Auth::login');
$routes->get('auth/verify-code',     'Auth::verifyCodePage');
$routes->post('auth/verify-code',    'Auth::verifyCode');
$routes->post('auth/resend-code',    'Auth::resendCode');
$routes->get('auth/logout',          'Auth::logout');

// ── Regular user dashboard ────────────────────────────────────────────────────
$routes->get('dashboard', 'Dashboard::index');

// ── Academic Records (CI4 local file server) ──────────────────────────────────
$routes->get('academic-records',                'AcademicRecords::index');
$routes->get('academic-records/list-folder',    'AcademicRecords::listFolder');
$routes->post('academic-records/create-folder', 'AcademicRecords::createFolder');
$routes->post('academic-records/upload',        'AcademicRecords::upload');
$routes->post('academic-records/upload-folder', 'AcademicRecords::uploadFolder');
$routes->get('academic-records/preview',        'AcademicRecords::preview');   // inline — no auto-download
$routes->get('academic-records/download',       'AcademicRecords::download');  // forces save-as
$routes->post('academic-records/delete-file',   'AcademicRecords::deleteFile');
$routes->post('academic-records/delete-folder', 'AcademicRecords::deleteFolder');
$routes->post('academic-records/rename',        'AcademicRecords::rename');
$routes->post('academic-records/move',          'AcademicRecords::move');

// ── Settings (all roles) ──────────────────────────────────────────────────────
$routes->get('settings',                  'Settings::index');
$routes->post('settings/update-profile',  'Settings::updateProfile');
$routes->post('settings/change-password', 'Settings::changePassword');
$routes->get('settings/activity-logs',    'Settings::activityLogs');

// ── Super Admin / privileged pages ───────────────────────────────────────────
// Note: checkAccess() inside each method enforces privileges.
// Admins always pass. Users only pass if the matching privilege is checked.
$routes->group('super-admin', function ($routes) {
    $routes->get('dashboard',                       'SuperAdmin::index');

    // Requires user_management privilege (or admin role)
    $routes->get('user-management',                 'SuperAdmin::userManagement');
    $routes->get('get-user/(:num)',                 'SuperAdmin::getUserData/$1');
    $routes->get('get-user-privileges/(:num)',      'SuperAdmin::getUserPrivileges/$1');
    $routes->post('add-admin',                      'SuperAdmin::addAdmin');
    $routes->post('edit-admin/(:num)',              'SuperAdmin::editAdmin/$1');
    $routes->post('delete-admin/(:num)',            'SuperAdmin::deleteAdmin/$1');
    $routes->get('delete-admin/(:num)',             'SuperAdmin::deleteAdmin/$1');
    $routes->post('approve-admin/(:num)',           'SuperAdmin::approveAdmin/$1');
    $routes->get('approve-admin/(:num)',            'SuperAdmin::approveAdmin/$1');
    $routes->post('reject-admin/(:num)',            'SuperAdmin::rejectAdmin/$1');
    $routes->get('reject-admin/(:num)',             'SuperAdmin::rejectAdmin/$1');
    $routes->post('toggle-suspend/(:num)',          'SuperAdmin::toggleSuspend/$1');
    $routes->get('toggle-suspend/(:num)',           'SuperAdmin::toggleSuspend/$1');
    $routes->post('update-user-privileges/(:num)', 'SuperAdmin::updateUserPrivileges/$1');

    // Requires audit_logs privilege (or admin role)
    $routes->get('activity-logs',                  'SuperAdmin::activityLogs');

    $routes->get('all-records',                    'SuperAdmin::allRecords');

    // Requires system_backup privilege (or admin role)
    $routes->get('system-backup',                  'SuperAdmin::systemBackup');

    $routes->get('settings',                       'Settings::index');
});