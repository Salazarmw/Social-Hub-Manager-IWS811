<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\Auth\TwoFactorVerifyController;
use App\Http\Controllers\Auth\TwoFactorSensitiveController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\CalendarController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->middleware('2fa.sensitive')->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->middleware('2fa.sensitive')->name('profile.destroy');
});


// OAuth entry points
Route::prefix('oauth')->group(function () {
    Route::get('{provider}/redirect', [OAuthController::class, 'redirect'])->name('oauth.redirect');
    Route::get('{provider}/callback', [OAuthController::class, 'callback'])->name('oauth.callback');
    Route::delete('{provider}/revoke', [OAuthController::class, 'revoke'])->middleware(['auth', 'verified', '2fa.sensitive'])->name('oauth.revoke');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Posts routes
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

    // Queue routes
    Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
    Route::delete('/queue/{id}', [QueueController::class, 'destroy'])->name('queue.destroy');

    // Calendar routes
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
    Route::get('/calendar/create', [CalendarController::class, 'create'])->name('calendar.create');
    Route::post('/calendar', [CalendarController::class, 'store'])->name('calendar.store');
    Route::get('/calendar/{id}', [CalendarController::class, 'show'])->name('calendar.show');
    Route::get('/calendar/{id}/edit', [CalendarController::class, 'edit'])->name('calendar.edit');
    Route::put('/calendar/{id}', [CalendarController::class, 'update'])->name('calendar.update');
    Route::delete('/calendar/{id}', [CalendarController::class, 'destroy'])->name('calendar.destroy');

    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings');

    // 2FA
    Route::prefix('2fa')->controller(TwoFactorController::class)->group(function () {
        Route::get('/', 'show')->name('2fa.show');
        Route::post('enable', 'enable')->middleware('2fa.sensitive')->name('2fa.enable');
        Route::post('disable', 'disable')->middleware('2fa.sensitive')->name('2fa.disable');
    });

    // 2FA verification after login
    Route::get('/2fa/verify', [TwoFactorVerifyController::class, 'show'])->name('2fa.verify');
    Route::post('/2fa/verify', [TwoFactorVerifyController::class, 'verify']);

    // 2FA verification for sensitive actions
    Route::get('/2fa/verify-sensitive', [TwoFactorSensitiveController::class, 'show'])->name('2fa.verify.sensitive');
    Route::post('/2fa/verify-sensitive', [TwoFactorSensitiveController::class, 'verify']);
});

require __DIR__ . '/auth.php';

// Ruta temporal para debug de variables de entorno
Route::get('/debug/env', function () {
    return response()->json([
        'X_API_KEY' => env('X_API_KEY'),
        'X_API_KEY_SECRET' => env('X_API_KEY_SECRET'),
        'APP_KEY' => env('APP_KEY'),
    ]);
});
