<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::get('/auth/eve/redirect', [AuthController::class, 'redirectToEve'])->name('eve.redirect');
Route::get('/auth/eve/callback', [AuthController::class, 'handleEveCallback'])->name('eve.callback');

Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::view('/{path?}', 'app')
    ->where('path', '^(?!api|storage|horizon|nova|telescope|_debugbar|build|assets).*$');

Route::fallback(static function () {
    abort(404);
});
