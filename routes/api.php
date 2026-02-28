<?php

use App\Modules\Auth\Http\Controllers\Api\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.stateful', 'throttle:10,1'])->prefix('auth')->group(function () {
    Route::post('/google', [GoogleAuthController::class, 'handle'])->name('api.auth.google');

    Route::middleware('auth')->group(function () {
        Route::get('/me', [GoogleAuthController::class, 'me'])->name('api.auth.me');
        Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('api.auth.logout');
    });
});

require base_path('app/Modules/Question/routes/api.php');
require base_path('app/Modules/Interview/routes/api.php');
