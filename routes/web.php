<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AgandaGroupController;
use App\Http\Controllers\Karyawan\KaryawanDashboardController;

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
    });

Route::middleware(['auth', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {
        Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])
            ->name('dashboard');
    });

Route::middleware(['auth', 'role:member'])
    ->prefix('member')
    ->name('member.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('member.dashboard');
        })->name('dashboard');
    });

Route::middleware(['auth', 'role:admin,karyawan,member'])
    ->prefix('aganda')
    ->name('aganda.')
    ->group(function () {
        Route::get('/groups', [AgandaGroupController::class, 'index'])
            ->name('groups.index');

        Route::get('/groups/{group}', [AgandaGroupController::class, 'show'])
            ->name('groups.show');

        Route::get('/groups/{group}/structure', [AgandaGroupController::class, 'structure'])
            ->name('groups.structure');

        Route::get('/groups/create', [AgandaGroupController::class, 'create'])
            ->name('groups.create');

        Route::post('/groups', [AgandaGroupController::class, 'store'])
            ->name('groups.store');
    });

Route::middleware(['auth', 'role:karyawan,member'])
    ->prefix('aganda')
    ->name('aganda.')
    ->group(function () {
        Route::get('/groups/{group}/members/create', [AgandaGroupController::class, 'createMember'])
            ->name('groups.members.create');

        Route::post('/groups/{group}/members', [AgandaGroupController::class, 'storeMember'])
            ->name('groups.members.store');
    });
