<?php

use App\Enums\TokenAbility;
use App\Modules\Interview\Http\Controllers\Api\InterviewSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:'.TokenAbility::AccessApi->value])->prefix('interview')->group(function () {
    Route::post('/complete', [InterviewSessionController::class, 'complete'])->name('api.interview.complete');
});
