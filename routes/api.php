<?php

use App\Enums\TokenAbility;
use App\Modules\Auth\Http\Controllers\Api\GoogleAuthController;
use App\Modules\Auth\Http\Controllers\Api\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:10,1'])->prefix('auth')->group(function () {
    Route::post('/login', LoginController::class)->name('api.auth.login');
    Route::post('/google', [GoogleAuthController::class, 'handle'])->name('api.auth.google');

    Route::post('/refresh', [GoogleAuthController::class, 'refresh'])
        ->middleware(['auth:sanctum', 'ability:'.TokenAbility::Refresh->value])
        ->name('api.auth.refresh');

    Route::middleware(['auth:sanctum', 'ability:'.TokenAbility::AccessApi->value])->group(function () {
        Route::get('/me', [GoogleAuthController::class, 'me'])->name('api.auth.me');
        Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('api.auth.logout');
    });
});

require base_path('app/Modules/Technology/routes/api.php');
require base_path('app/Modules/Question/routes/api.php');
require base_path('app/Modules/Interview/routes/api.php');
