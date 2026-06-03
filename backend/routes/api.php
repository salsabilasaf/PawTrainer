<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Gateway\CategoryController;
use App\Http\Controllers\Api\Gateway\CommentController;
use App\Http\Controllers\Api\Gateway\ExternalApiController;
use App\Http\Controllers\Api\Gateway\FavoriteController;
use App\Http\Controllers\Api\Gateway\TutorialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PawTrainer API Routes
|--------------------------------------------------------------------------
|
| Auth Guard : jwt (dikonfigurasi di config/auth.php)
| Response   : App\Helpers\ResponseHelper (konsisten JSON)
|
*/

// ════════════════════════════════════════════════════════════════════════════
// PUBLIC ROUTES — tidak perlu token
// ════════════════════════════════════════════════════════════════════════════

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ════════════════════════════════════════════════════════════════════════════
// PROTECTED ROUTES — wajib bearer token JWT
// ════════════════════════════════════════════════════════════════════════════

Route::middleware('api.token')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/profile',      [AuthController::class, 'profile']);

    // ══════════════════════════════════════════════════════════════════════
    // API GATEWAY — prefix: /api/gateway
    // ══════════════════════════════════════════════════════════════════════

    Route::prefix('gateway')->group(function () {

        // Admin + User: bisa lihat tutorial, category, comment, favorite
        Route::get('tutorials',                           [TutorialController::class,    'index']);
        Route::get('tutorials/{id}',                      [TutorialController::class,    'show']);
        Route::get('categories',                          [CategoryController::class,    'index']);
        Route::get('categories/{id}',                     [CategoryController::class,    'show']);
        Route::get('tutorials/{tutorialId}/comments',     [CommentController::class,     'index']);
        Route::get('favorites',                           [FavoriteController::class,    'index']);

        // External APIs — semua user login bisa akses
        Route::get('breeds',           [ExternalApiController::class, 'catBreeds']);
        Route::get('facts',            [ExternalApiController::class, 'catFacts']);
        Route::get('videos/{keyword}', [ExternalApiController::class, 'youtubeVideos']);

        // Admin + User: create/delete comment, toggle favorite
        Route::middleware('role:admin,user')->group(function () {
            Route::post('comments',        [CommentController::class,  'store']);
            Route::delete('comments/{id}', [CommentController::class,  'destroy']);
            Route::post('comments/reply', [CommentController::class, 'store']);
            Route::post('favorites',       [FavoriteController::class, 'store']);
        });

        // Admin Only: CRUD tutorial & category
        Route::middleware('role:admin')->group(function () {
            Route::post('tutorials',         [TutorialController::class, 'store']);
            Route::match(['PUT','POST'],'tutorials/{id}', [TutorialController::class, 'update']);
            Route::delete('tutorials/{id}',  [TutorialController::class, 'destroy']);

            Route::post('categories',        [CategoryController::class, 'store']);
            Route::put('categories/{id}',    [CategoryController::class, 'update']);
            Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
        });

    }); // end gateway

}); // end api.token