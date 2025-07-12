<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasbonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
|
*/

// Guest Routes (Belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes (Sudah login)
Route::middleware('auth.login')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', fn() => redirect('/dashboard'))->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('settings.profile.update');

    // Super Admin Routes
    Route::middleware('super_admin')->group(function () {
        Route::prefix('settings')->as('settings.')->group(function () {
            Route::resource('roles', RoleController::class);
            Route::resource('users', UserController::class);
        });
    });

    // Kasbon Routes
    Route::prefix('settings')->group(function () {
        Route::resource('kasbons', KasbonController::class)->names('settings.kasbons')->middleware('kasbon.access');

        // Override edit & update dengan tambahan middleware approve
        Route::get('kasbons/{kasbon}/edit', [KasbonController::class, 'edit'])
            ->name('kasbons.edit')
            ->middleware('kasbon.approve');

        Route::put('kasbons/{kasbon}', [KasbonController::class, 'update'])
            ->name('kasbons.update')
            ->middleware('kasbon.approve');
    });
});