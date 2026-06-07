<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PresenceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/friends', [FriendController::class, 'index'])->name('friends.index');
    Route::post('/friends/search', [FriendController::class, 'search'])->name('friends.search');
    Route::post('/friends', [FriendController::class, 'store'])->name('friends.store');
    Route::patch('/friends/{friendship}', [FriendController::class, 'accept'])->name('friends.accept');
    Route::delete('/friends/{friendship}', [FriendController::class, 'destroy'])->name('friends.destroy');
    Route::post('/presence/ping', [PresenceController::class, 'ping'])->name('presence.ping');
});

Route::get('/four-hundred', function () {
    return view('four-hundred');
});

Route::get('/400', function () {
    return redirect('/four-hundred');
});

Route::get('/galaxy-bowling', function () {
    return view('galaxy-bowling');
});

Route::get('/game', function () {
    return redirect('/');
});

Route::get('/platformer', function () {
    return view('platformer');
});

Route::get('/tic-tac-toe', function () {
    return view('tic-tac-toe');
});

Route::get('/board-game', function () {
    return view('board-game');
});

Route::get('/whack-a-mole', function () {
    return view('whack-a-mole');
});

Route::get('/chess', function () {
    return view('chess');
});

Route::get('/uno', function () {
    return view('uno');
});

Route::get('/bowling', function () {
    return redirect('/galaxy-bowling');
});