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
$routes->get( 'auth/mpin-entry',     'Auth::mpinEntryPage');
$routes->post('auth/mpin-entry',     'Auth::verifyMpin');
$routes->post('auth/send-approval-request', 'Auth::sendApprovalRequest');
$routes->get('auth/logout',          'Auth::logout');
$routes->get('auth/logout',          'Auth::logout');

    // ── Password / Username Recovery ─────────────────────────────────────────
    $routes->get( 'auth/forgot',                 'Recovery::forgotPage');
    $routes->post('auth/forgot',                 'Recovery::sendRecoveryOtp');
    $routes->get( 'auth/forgot-verify',          'Recovery::verifyOtpPage');
    $routes->post('auth/forgot-verify',          'Recovery::verifyOtp');
    $routes->get( 'auth/recovery-dashboard',     'Recovery::recoveryDashboard');
    $routes->post('auth/reset-password',         'Recovery::resetPassword');

    // ── Forgot MPIN ──────────────────────────────────────────────────────────
    $routes->get( 'auth/forgot-mpin',            'Recovery::forgotMpinPage');
    $routes->post('auth/forgot-mpin',            'Recovery::sendMpinOtp');
    $routes->get( 'auth/forgot-mpin-verify',     'Recovery::verifyMpinOtpPage');
    $routes->post('auth/forgot-mpin-verify',     'Recovery::verifyMpinOtp');
    $routes->get( 'auth/reset-mpin',             'Recovery::resetMpinPage');
    $routes->post('auth/reset-mpin',             'Recovery::resetMpin');



// ── Regular user dashboard ────────────────────────────────────────────────────
$routes->get('dashboard', 'Dashboard::index');

// ── Academic Records (CI4 local file server) ──────────────────────────────────
$routes->get('academic-records',                'AcademicRecords::index');
$routes->get('academic-records/list-folder',    'AcademicRecords::listFolder');
$routes->get('academic-records/list-all-folders', 'AcademicRecords::listAllFolders');
$routes->post('academic-records/create-folder', 'AcademicRecords::createFolder');
$routes->post('academic-records/upload',        'AcademicRecords::upload');
$routes->post('academic-records/upload-folder', 'AcademicRecords::uploadFolder');
$routes->get('academic-records/preview',        'AcademicRecords::preview');   // inline — no auto-download
$routes->get('academic-records/download',       'AcademicRecords::download');  // forces save-as
$routes->post('academic-records/delete-file',   'AcademicRecords::deleteFile');
$routes->post('academic-records/delete-folder', 'AcademicRecords::deleteFolder');
$routes->post('academic-records/rename',        'AcademicRecords::rename');
$routes->post('academic-records/move',          'AcademicRecords::move');
$routes->post('academic-records/temp-upload',       'AcademicRecords::tempUpload');
$routes->get('academic-records/preview-pending/(:any)', 'AcademicRecords::previewPending/$1');
$routes->get('academic-records/download-pending/(:any)', 'AcademicRecords::downloadPending/$1');
$routes->post('academic-records/cancel-pending',    'AcademicRecords::cancelPending');
$routes->post('academic-records/finalize-upload',   'AcademicRecords::finalizeUpload');
$routes->get('academic-records/ocr-result/(:any)', 'AcademicRecords::getOcrResult/$1');
$routes->get('academic-records/test-ocr', 'AcademicRecords::testOcr');
$routes->get('academic-records/metadata-search', 'AcademicRecords::metadataSearch');

// ── Settings (all roles) ──────────────────────────────────────────────────────
$routes->get('settings',                  'Settings::index');
$routes->post('settings/update-profile',  'Settings::updateProfile');
$routes->post('settings/change-password', 'Settings::changePassword');
$routes->get('settings/activity-logs',    'Settings::activityLogs');
$routes->post('settings/change-mpin',     'Settings::changeMpin');


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
    $routes->get( 'group-privileges',  'SuperAdmin::getGroupPrivileges');
    $routes->post('set-mpin/(:num)',   'SuperAdmin::setUserMpin/$1');
    $routes->get( 'get-user-folders/(:num)',    'SuperAdmin::getUserFolders/$1');
    $routes->post('update-user-folders/(:num)', 'SuperAdmin::updateUserFolders/$1');
    $routes->get('users-by-role/(:alpha)',      'SuperAdmin::getUsersByRole/$1');
    // Document Types (OCR keyword management)
$routes->get( 'record-types',                        'RecordTypes::index');
$routes->post('record-types/save-type',              'RecordTypes::saveType');
$routes->get( 'record-types/delete-type/(:num)',     'RecordTypes::deleteType/$1');
$routes->get( 'record-types/keywords/(:num)',        'RecordTypes::getKeywords/$1');
$routes->post('record-types/save-keyword',           'RecordTypes::saveKeyword');
$routes->post('record-types/delete-keyword/(:num)',  'RecordTypes::deleteKeyword/$1');


    // Requires audit_logs privilege (or admin role)
    $routes->get('activity-logs',                  'SuperAdmin::activityLogs');

    $routes->get('all-records',                    'SuperAdmin::allRecords');

    // Requires system_backup privilege (or admin role)
    $routes->get('system-backup',                  'SuperAdmin::systemBackup');

    $routes->get('settings',                       'Settings::index');

     // System Backup API routes
    $routes->post('backup/run',        'Backup::run');
    $routes->get( 'backup/download',   'Backup::download');
    $routes->get( 'backup/list',       'Backup::listBackups');
    $routes->get( 'backup/schedule',   'Backup::schedule');
    $routes->post('backup/schedule',   'Backup::schedule');
    $routes->post('backup/delete',     'Backup::deleteBackup');
});

$routes->get('super-admin/backup/cron', 'Backup::cron');