<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CommentController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Posts
Route::get('/bai-viet', [PostController::class, 'index'])->name('posts.index');
Route::get('/bai-viet/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login']);
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register']);
});

Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Comments
    Route::post('/bai-viet/{post}/binh-luan', [PostController::class, 'comment'])->name('posts.comment');

    // Favorites
    Route::post('/bai-viet/{post}/yeu-thich', [PostController::class, 'toggleFavorite'])->name('posts.favorite');
    Route::get('/yeu-thich', [PostController::class, 'favorites'])->name('posts.favorites');

    // Rating
    Route::post('/bai-viet/{post}/danh-gia', [PostController::class, 'rate'])->name('posts.rate');

    // Profile
    Route::get('/ho-so', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/ho-so', [ProfileController::class, 'update'])->name('profile.update');
});