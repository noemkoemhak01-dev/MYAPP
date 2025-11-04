<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public article routes
Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/trending', [ArticleController::class, 'trending']);
    Route::get('/latest', [ArticleController::class, 'latest']);
    Route::get('/category/{categorySlug}', [ArticleController::class, 'byCategory']);
    Route::get('/{id}', [ArticleController::class, 'show']);
    Route::get('/slug/{slug}', [ArticleController::class, 'showBySlug']);
    Route::post('/{id}/view', [ArticleController::class, 'incrementView']);
});

// Public category routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

// Global search
Route::get('/search', [ArticleController::class, 'search']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/avatar', [AuthController::class, 'uploadAvatar']);
    });

    // User routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::get('/articles', [UserController::class, 'articles']);
        Route::get('/bookmarks', [UserController::class, 'bookmarks']);
    });

    // Bookmark routes
    Route::prefix('bookmarks')->group(function () {
        Route::get('/', [BookmarkController::class, 'index']);
        Route::post('/{articleId}', [BookmarkController::class, 'store']);
        Route::delete('/{articleId}', [BookmarkController::class, 'destroy']);
        Route::post('/{articleId}/toggle', [BookmarkController::class, 'toggle']);
    });

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unreadCount']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Admin routes
    Route::middleware('admin')->group(function () {
        // Article management
        Route::post('/articles', [ArticleController::class, 'store']);
        Route::put('/articles/{id}', [ArticleController::class, 'update']);
        Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);

        // Category management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });
});