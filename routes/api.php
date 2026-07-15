<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| OCP StockElec — API Routes
|--------------------------------------------------------------------------
|
| All routes use JSON responses. Protected routes require a valid Sanctum
| Bearer token obtained from POST /api/login.
|
*/

// ── Public ────────────────────────────────────────────────────────────────
Route::post('login', [AuthController::class, 'login']);

// ── Protected (Sanctum) ───────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth / Profile
    Route::post('logout',             [AuthController::class, 'logout']);
    Route::get('profile',             [AuthController::class, 'profile']);
    Route::put('profile',             [AuthController::class, 'updateProfile']);
    Route::put('profile/password',    [AuthController::class, 'updatePassword']);

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('stats',            [DashboardController::class, 'stats']);
        Route::get('transfers-chart',  [DashboardController::class, 'transfersChart']);
        Route::get('categories',       [DashboardController::class, 'categories']);
        Route::get('recent-articles',  [DashboardController::class, 'recentArticles']);
        Route::get('recent-movements', [DashboardController::class, 'recentMovements']);
        Route::get('top-transferred',  [DashboardController::class, 'topTransferred']);
        Route::get('alerts',           [DashboardController::class, 'alerts']);
    });

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Articles — order matters: specific paths before {article}
    Route::get('articles/trashed',          [ArticleController::class, 'trashed']);
    Route::get('articles/scan/{code}',      [ArticleController::class, 'scan']);
    Route::put('articles/{id}/restore',     [ArticleController::class, 'restore']);
    Route::delete('articles/{id}/force',    [ArticleController::class, 'forceDestroy']);
    Route::post('articles/{article}/transfer', [ArticleController::class, 'transfer']);
    Route::apiResource('articles', ArticleController::class);

    // Transfers (list & show — creation happens via articles/{id}/transfer)
    Route::get('transfers',             [TransferController::class, 'index']);
    Route::get('transfers/{transfer}',  [TransferController::class, 'show']);

    // Users (admin / manager only — enforced by FormRequest authorize())
    Route::apiResource('users', UserController::class)->except(['show']);
});
