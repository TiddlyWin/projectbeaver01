<?php

use App\Http\Controllers\Api\Account\AccountController;
use App\Http\Controllers\Api\Account\ProfileController;
use App\Http\Controllers\CharacterController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [ProfileController::class, 'me']);

    Route::group(['prefix' => '/account'], static function() {
        Route::post('/register', [AccountController::class, 'registerEmail']);
    });

    Route::post('/characters/{character}/set-main', [CharacterController::class, 'setMain']);
});
