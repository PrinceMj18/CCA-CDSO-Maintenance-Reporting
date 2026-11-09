<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MaintenanceImageController;
use App\Http\Controllers\ArchiveController;

// AUTH
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'doLogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/dashboard/superadmin', [SuperAdminController::class, 'superadminDashboard'])->name('superadminDashboard');
    Route::get('/manage-account/superadmin/create', [SuperAdminController::class, 'createAccount'])->name('superadmin.accounts.create');
    Route::post('/manage-account/superadmin/create', [SuperAdminController::class, 'register'])->name('superadmin.accounts.store');
    Route::get('/archives/accounts/superadmin', [ArchiveController::class, 'accounts'])->name('superadmin.archives.accounts');
    Route::post('/archives/accounts/superadmin/{id}/restore', [ArchiveController::class, 'restoreAccount'])->name('superadmin.archives.accounts.restore');
    Route::get('/archives/requests/superadmin', [ArchiveController::class, 'requests'])->name('superadmin.archives.requests');
    Route::post('/archives/requests/superadmin/{id}/restore', [ArchiveController::class, 'restoreRequest'])->name('superadmin.archives.requests.restore');
    Route::post('/maintenance-request/superadmin/{id}/archive', [ArchiveController::class, 'archiveRequest'])->name('superadmin.requests.archive');
    Route::get('/manage-account/superadmin', [SuperAdminController::class, 'accManagement'])->name('acc_superadmin');
    Route::get('/edit-account/superadmin/{id}', [SuperAdminController::class, 'edit_account'])->name('edit_account');
    Route::put('/update-account/superadmin/{id}', [SuperAdminController::class, 'update_account'])->name('update_account');
        Route::delete('/delete-account/superadmin/{id}', [SuperAdminController::class, 'delete_account'])
        ->name('delete_account');
});

// NOTIFICATIONS (available to all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/maintenance-images/{image}', [MaintenanceImageController::class, 'show'])
        ->name('maintenance-images.show');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])
        ->name('notifications.readAll');
});

// USER
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/dashboard/user', [UserController::class, 'userDashboard'])->name('userDashboard');
    Route::get('/form', [UserController::class, 'showForm'])->name('showForm');
    Route::get('/track-request', [UserController::class, 'trackReq'])->name('trackReq');
    Route::get('/request/{id}', [UserController::class, 'viewRequest'])->name('viewRequest.user');
    Route::post('/request/store', [UserController::class, 'requestReport']);
});

// ADMIN
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard/admin', [AdminController::class, 'adminDashboard'])->name('adminDashboard');
    Route::get('/archives/requests/admin', [ArchiveController::class, 'requests'])->name('admin.archives.requests');
    Route::post('/archives/requests/admin/{id}/restore', [ArchiveController::class, 'restoreRequest'])->name('admin.archives.requests.restore');
    Route::post('/maintenance-request/admin/{id}/archive', [ArchiveController::class, 'archiveRequest'])->name('admin.requests.archive');
    Route::get('/dashboard/admin/analytics', [AdminController::class, 'dashboardAnalytics'])->name('adminDashboard.analytics');

    Route::get('/maintenance-request/admin', [AdminController::class, 'maintenanceRequest'])->name('maintenanceRequest.admin');
    Route::get('/view-request/admin/{id}', [AdminController::class, 'viewRequest'])
    ->name('viewRequest.admin');
    Route::put('/update-request/admin/{id}', [AdminController::class, 'updateRequest'])
        ->name('updateRequest.admin');

    Route::get('/logs-request/admin', [AdminController::class, 'logs'])->name('logsRequest');
    Route::get('/view-history-request/admin/{id}', [AdminController::class, 'viewHistoryRequest'])
    ->name('viewHistoryRequest.admin');;

    Route::post('/save_inventory', [AdminController::class, 'save_inventory'])
    ->name('save_inventory');

    Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
    Route::get('/inventory/edit/{id}', [AdminController::class, 'edit_inventory'])->name('edit_inventory');
    Route::put('/inventory/update/{id}', [AdminController::class, 'update_inventory'])->name('update_inventory');
    Route::delete('/inventory/delete/{id}', [AdminController::class, 'delete_inventory'])->name('delete_inventory');


    Route::get('/admin/backup', [BackupController::class, 'index'])
        ->name('admin.backup');

    Route::post('/admin/backup/create', [BackupController::class, 'backup'])
        ->name('admin.backup.create');

    Route::post('/admin/backup/restore', [BackupController::class, 'restore'])
        ->name('admin.backup.restore');
});

//Personnel
Route::middleware(['auth', 'personnel'])->group(function () {
    Route::get('/dashboard/personnel', [PersonnelController::class, 'personnelDashboard'])->name('personnelDashboard');
    Route::get('/maintenance-request/personnel', [PersonnelController::class, 'maintenanceRequest'])->name('maintenanceRequest.personnel');
    Route::get('/view-request/personnel/{id}', [PersonnelController::class, 'viewRequest'])
    ->name('viewRequest.personnel');
    Route::put('/update-request/personnel/{id}', [PersonnelController::class, 'updateRequest'])
    ->name('updateRequest.personnel');
    Route::post('/return-materials/personnel/{id}', [PersonnelController::class, 'returnMaterials'])
    ->name('returnMaterials.personnel');
});

