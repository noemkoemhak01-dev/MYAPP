<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\FeedPostController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
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

// Feed routes powering Flutter UI
Route::prefix('feed')->group(function () {
    Route::get('/posts', [FeedPostController::class, 'index']);
    Route::get('/posts/trending', [FeedPostController::class, 'trending']);
    Route::get('/posts/latest', [FeedPostController::class, 'latest']);
    Route::get('/posts/slug/{slug}', [FeedPostController::class, 'showBySlug']);
    Route::get('/posts/{post}', [FeedPostController::class, 'show']);
    Route::post('/posts/{post}/view', [FeedPostController::class, 'incrementView']);

    Route::get('/topics', [FeedPostController::class, 'topics']);
});

// Search feed posts
Route::get('/search', [FeedPostController::class, 'search']);

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

    // User routes aligned with profile tab
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::get('/posts', [UserController::class, 'posts']);
        Route::get('/bookmarks', [UserController::class, 'bookmarks']);
    });

    // Bookmark routes
    Route::prefix('bookmarks')->group(function () {
        Route::get('/', [BookmarkController::class, 'index']);
        Route::post('/{post}', [BookmarkController::class, 'store']);
        Route::delete('/{post}', [BookmarkController::class, 'destroy']);
        Route::post('/{post}/toggle', [BookmarkController::class, 'toggle']);
    });

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unreadCount']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Profile analytics
    Route::get('/profile/overview', [ProfileController::class, 'show']);
    Route::get('/profile/activity', [ProfileController::class, 'activity']);

    // Feed management
    Route::prefix('feed')->group(function () {
        Route::post('/posts', [FeedPostController::class, 'store']);
        Route::put('/posts/{post}', [FeedPostController::class, 'update']);
        Route::delete('/posts/{post}', [FeedPostController::class, 'destroy']);
        Route::post('/posts/{post}/like', [FeedPostController::class, 'toggleLike']);
    });
});