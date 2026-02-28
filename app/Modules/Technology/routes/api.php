<?php

use App\Modules\Technology\Http\Controllers\Api\TechnologyController;
use Illuminate\Support\Facades\Route;

Route::get('/technologies', [TechnologyController::class, 'index'])->name('api.technologies.index');
