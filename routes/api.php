<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CharacterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [ProfileController::class, 'me']);
    Route::post('/characters/{character}/set-main', [CharacterController::class, 'setMain']);
});
