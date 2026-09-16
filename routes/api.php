<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\StructureController;
use App\Http\Controllers\Api\PairingController;
use App\Http\Controllers\Api\BonusController;
use App\Http\Controllers\Api\WithdrawalController;
use App\Http\Controllers\Api\AdminWithdrawalController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::get('/groups/{group}', [GroupController::class, 'show']);
    Route::delete('/groups/{group}', [GroupController::class, 'destroy']);
    Route::get('/groups/{group}/members/create', [MemberController::class, 'create']);
    Route::post('/groups/{group}/members', [MemberController::class, 'store']);
    Route::get('/groups/{group}/structure', [StructureController::class, 'show']);
    Route::get('/groups/{group}/pairing/create', [PairingController::class, 'create']);
    Route::post('/groups/{group}/pairing', [PairingController::class, 'store']);
    Route::get('/bonus', [BonusController::class, 'index']);
    Route::get('/bonus/history', [BonusController::class, 'history']);
    Route::post('/bonus/allocate-package', [BonusController::class, 'allocatePackage']);
    Route::get('/withdrawals', [WithdrawalController::class, 'index']);
    Route::post('/withdrawals', [WithdrawalController::class, 'store']);
    Route::get('/admin/withdrawals', [AdminWithdrawalController::class, 'index']);
    Route::put('/admin/withdrawals/{withdrawal}/approve', [AdminWithdrawalController::class, 'approve']);
    Route::put('/admin/withdrawals/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject']);
    Route::put('/admin/withdrawals/{withdrawal}/paid', [AdminWithdrawalController::class, 'paid']);
});
