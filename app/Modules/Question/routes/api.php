<?php

use App\Enums\TokenAbility;
use App\Modules\Question\Http\Controllers\Api\QuestionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('questions')->group(function () {
    Route::get('/', [QuestionController::class, 'index'])->name('api.questions.index');
    Route::post('/{question}/rate', [QuestionController::class, 'rate'])->name('api.questions.rate');
});
