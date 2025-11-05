<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/**
 * Panel Admin (protegido)
 */
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings/global', [SettingController::class, 'updateGlobal'])->name('admin.settings.global.update');
    Route::post('/admin/settings/banned', [SettingController::class, 'addBanned'])->name('admin.settings.banned.add');
    Route::delete('/admin/settings/banned/{bannedExtension}', [SettingController::class, 'deleteBanned'])->name('admin.settings.banned.delete');
    Route::get('/admin/settings/groups', [SettingController::class, 'groupLimits'])->name('admin.settings.group.limits');
    Route::post('/admin/settings/groups', [SettingController::class, 'saveGroupLimit'])->name('admin.settings.group.save');
    Route::delete('/admin/settings/groups/{groupLimit}', [SettingController::class, 'deleteGroupLimit'])->name('admin.settings.group.delete');
    Route::get('/admin/settings/users', [SettingController::class, 'userLimits'])->name('admin.settings.user.limits');
    Route::post('/admin/settings/users', [SettingController::class, 'saveUserLimit'])->name('admin.settings.user.save');
    Route::delete('/admin/settings/users/{userLimit}', [SettingController::class, 'deleteUserLimit'])->name('admin.settings.user.delete');
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users/{user}/groups', [UserController::class, 'assignGroup'])->name('admin.users.assignGroup');
    Route::delete('/admin/users/{user}/groups/{group}', [UserController::class, 'removeGroup'])->name('admin.users.removeGroup');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/files', [\App\Http\Controllers\FileUploadController::class, 'index'])
        ->name('files.index');

    Route::post('/files/upload', [\App\Http\Controllers\FileUploadController::class, 'store'])
        ->name('files.upload');

    Route::get('/files/{fileItem}/download', [\App\Http\Controllers\FileUploadController::class, 'download'])
    ->name('files.download');

    Route::delete('/files/{fileItem}', [\App\Http\Controllers\FileUploadController::class, 'destroy'])
    ->name('files.destroy');
});

require __DIR__.'/auth.php';
