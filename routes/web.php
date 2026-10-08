<?php

use App\Http\Controllers\Admin\BonusController;
use App\Http\Controllers\Admin\BonusController as AdminBonusController;
use App\Http\Controllers\Admin\CalonController;
use App\Http\Controllers\Admin\CalonPaymentController;
use App\Http\Controllers\Admin\CalonPaymentController as AdminCalonPaymentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\AgandaGroupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Karyawan\KaryawanDashboardController;
use App\Http\Controllers\MemberFinanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\BonusWithdrawalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homepage');
})->name('homepage');

Route::get('/brosur', function () {
    return redirect('/#brosur');
})->name('brosur');

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('password.email');

Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/members', [MemberController::class, 'index'])
            ->name('members.index');

        Route::get('/calons/create', [CalonController::class, 'create'])
            ->name('calons.create');

        Route::post('/calons', [CalonController::class, 'store'])
            ->name('calons.store');

Route::get('/bonus/withdrawals', [BonusWithdrawalController::class, 'index'])
    ->name('bonus.withdrawals.index');

Route::get('/bonus/withdrawals/{withdrawal}', [BonusWithdrawalController::class, 'show'])
    ->name('bonus.withdrawals.show');

Route::post('/bonus/withdrawals/{withdrawal}/approve', [BonusWithdrawalController::class, 'approve'])
    ->name('bonus.withdrawals.approve');

Route::post('/bonus/withdrawals/{withdrawal}/paid', [BonusWithdrawalController::class, 'paid'])
    ->name('bonus.withdrawals.paid');

Route::post('/bonus/withdrawals/{withdrawal}/reject', [BonusWithdrawalController::class, 'reject'])
    ->name('bonus.withdrawals.reject');

            });

        Route::prefix('rewards')
            ->name('rewards.')
            ->controller(RewardController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/export', 'export')->name('export');
                Route::get('/history', 'history')->name('history');
                Route::get('/{group}/members/{user}', 'show')->name('show');
                Route::post('/{group}/members/{user}/levels/{level}/verify', 'verify')->name('verify');
            });

        Route::prefix('bonus')
            ->name('bonus.')
            ->group(function () {
                Route::get('/', [BonusController::class, 'index'])
                    ->name('index');

                Route::get('/payments', [CalonPaymentController::class, 'index'])
                    ->name('payments.index');

                Route::get('/payments/create', [CalonPaymentController::class, 'create'])
                    ->name('payments.create');

                Route::post('/payments', [CalonPaymentController::class, 'store'])
                    ->name('payments.store');

                Route::post('/users/{user}/allocate-bonus', [CalonPaymentController::class, 'allocateBonus'])
                    ->name('users.allocate-bonus');
    });

Route::middleware(['auth', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {
        Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/members', [AdminMemberController::class, 'index'])
            ->name('members.index');

        Route::get('/bonus', [AdminBonusController::class, 'index'])
            ->name('bonus.index');

        Route::get('/payments', [AdminCalonPaymentController::class, 'index'])
            ->name('payments.index');
    });

Route::middleware(['auth', 'role:member'])
    ->prefix('member')
    ->name('member.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('member.dashboard');
        })->name('dashboard');

        Route::get('/bonus', [MemberFinanceController::class, 'bonus'])
            ->name('finance.bonus');

        Route::get('/payments', [MemberFinanceController::class, 'payments'])
            ->name('finance.payments');
    });

Route::middleware(['auth', 'role:admin,karyawan,member'])
    ->prefix('aganda')
    ->name('aganda.')
    ->group(function () {
        Route::get('/groups', [AgandaGroupController::class, 'index'])
            ->name('groups.index');

        Route::get('/groups/create', [AgandaGroupController::class, 'create'])
            ->name('groups.create');

        Route::post('/groups', [AgandaGroupController::class, 'store'])
            ->name('groups.store');

        Route::get('/rewards', function () {
            return view('aganda.rewards.index');
        })->name('rewards.index');

        Route::delete('/groups/{group}/members/{member}', [AgandaGroupController::class, 'cancelMember'])
            ->name('groups.members.cancel');

        Route::get('/groups/{group}', [AgandaGroupController::class, 'show'])
            ->name('groups.show');

        Route::get('/groups/{group}/structure', [AgandaGroupController::class, 'structure'])
            ->name('groups.structure');

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::post('/groups/{group}/pair', [AgandaGroupController::class, 'pair'])
            ->name('groups.pair');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('aganda')
    ->name('aganda.')
    ->group(function () {
        Route::get('/structures', [AgandaGroupController::class, 'allStructures'])
            ->name('structures.admin');

        Route::get('/groups/{group}/member/{user}/structure', [AgandaGroupController::class, 'adminMemberStructure'])
            ->name('admin.member.structure');
    });

Route::middleware(['auth', 'role:karyawan,member'])
    ->prefix('aganda')
    ->name('aganda.')
    ->group(function () {
        Route::get('/my-structure', [AgandaGroupController::class, 'myStructure'])
            ->name('structures.index');

        Route::get('/groups/{group}/members/create', [AgandaGroupController::class, 'createMember'])
            ->name('groups.members.create');

        Route::post('/groups/{group}/members', [AgandaGroupController::class, 'storeMember'])
            ->name('groups.members.store');

        Route::delete('/groups/{group}', [AgandaGroupController::class, 'destroy'])
            ->name('groups.destroy');
    });
