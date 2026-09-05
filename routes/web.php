<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TweetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [TweetController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/tweet', [TweetController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('tweet.store');

Route::get('/tweet/{tweet}/show', [TweetController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('tweet.show');

Route::get('/tweet/{tweet}/edit', [TweetController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('tweet.edit');

Route::put('/tweet/{tweet}/update', [TweetController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('tweet.update');

Route::delete('/tweet/{tweet}/destroy', [TweetController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('tweet.destroy');

Route::post('/tweet/{tweet}/comment', [CommentController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('comment.store');

Route::get('/tweet/{tweet}/comment/{comment}/edit', [CommentController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('comment.edit');

Route::put('/tweet/{tweet}/comment/{comment}', [CommentController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('comment.update');

Route::delete('/tweet/{tweet}/comment/{comment}', [CommentController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('comment.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
