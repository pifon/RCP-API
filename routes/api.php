<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Welcome, ping route
Route::get('/', function () {
    return response()->json([
        'message' => sprintf('Welcome to %s API.', ucfirst(config('app.name'))),
        200,
    ]);
})->name('welcome');

Route::post('login', [AuthController::class, 'login'])->name('login');
// Route::post('/register', [AuthController::class, 'register'])->name('register');

// TODO - remove
Route::prefix('recipes')->name('recipes.')->group(function () {
    Route::get('/create', App\Http\Controllers\v1\Recipe\Create::class)->name('create');
    Route::get('/{slug}', \App\Http\Controllers\v1\Recipe\Show::class)->name('show');
    Route::get('/{slug}/details', \App\Http\Controllers\v1\Recipe\Details::class)->name('details');
});
