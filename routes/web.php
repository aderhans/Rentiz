<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page — sekarang langsung tampilkan form login/register
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('landing', ['activeTab' => 'login']);
})->name('landing');

/* --------------------------------------------------------
 * Authentication Routes (Guest Only)
 * -------------------------------------------------------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/* --------------------------------------------------------
 * Authenticated Routes
 * -------------------------------------------------------- */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/switch-mode', [AuthController::class, 'switchMode'])->name('switch-mode');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
});
