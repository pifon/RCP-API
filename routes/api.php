<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Welcome, ping route
Route::get('/', function (){
    return response()->json([
        "message" => sprintf("Welcome to %s API.", ucfirst(config('app.name'))),
        200
    ]);
});

Route::post('/login',[AuthController::class, 'login']);
Route::post('/register',[AuthController::class, 'register']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('jwt.guard.authenticate');

Route::prefix('cuisines')->name('cuisines.')->group(function () {
    Route::get('/', App\Http\Controllers\Cuisine\Catalog::class)->name('list');
    Route::get('/{slug}', App\Http\Controllers\Cuisine\Show::class)->name('show');
    Route::get('/{slug}/details', App\Http\Controllers\Cuisine\Details::class)->name('details');
    Route::get('/{slug}/recipes', App\Http\Controllers\Cuisine\Recipes::class)->name('recipes');
    Route::get('/{slug}/authors', App\Http\Controllers\Cuisine\Authors::class)->name('authors');
    //Route::get('/{slug}/ingredients', App\Http\Controllers\Cuisine\Ingredients::class)->name('ingredients');
    //Route::get('/{slug}/related', App\Http\Controllers\Cuisine\Related::class)->name('related');
});

Route::prefix('authors')->name('authors.')->group(function () {
    Route::get('/{username}', App\Http\Controllers\Author\Show::class)->name('show');
    Route::get('/{username}/details', App\Http\Controllers\Author\Details::class)->name('details');
});

Route::prefix('recipes')->name('recipes.')->group(function () {
    Route::get('/{slug}', App\Http\Controllers\Recipe\Show::class)->name('show');
    Route::get('/{slug}/details', App\Http\Controllers\Recipe\Details::class)->name('details');
});