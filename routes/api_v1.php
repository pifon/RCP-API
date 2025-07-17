<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('cuisines')->name('cuisines.')->group(function () {
    Route::get('/', \App\Http\Controllers\v1\Cuisine\Catalog::class)->name('list');
    Route::get('/{slug}', \App\Http\Controllers\v1\Cuisine\Show::class)->name('show');
    Route::get('/{slug}/details', \App\Http\Controllers\v1\Cuisine\Details::class)->name('details');
    Route::get('/{slug}/recipes', \App\Http\Controllers\v1\Cuisine\Recipes::class)->name('recipes');
    Route::get('/{slug}/authors', \App\Http\Controllers\v1\Cuisine\Authors::class)->name('authors');
    // Route::get('/{slug}/ingredients', App\Http\Controllers\Cuisine\Ingredients::class)->name('ingredients');
    // Route::get('/{slug}/related', App\Http\Controllers\Cuisine\Related::class)->name('related');
});

Route::prefix('authors')->name('authors.')->group(function () {
    Route::get('/{username}', \App\Http\Controllers\v1\Author\Show::class)->name('show');
    Route::get('/{username}/details', \App\Http\Controllers\v1\Author\Details::class)->name('details');
});

Route::prefix('recipes')->name('recipes.')->group(function () {
    Route::post('/create', App\Http\Controllers\v1\Recipe\Create::class)->name('create');
    Route::get('/{slug}', \App\Http\Controllers\v1\Recipe\Show::class)->name('show');
    Route::get('/{slug}/details', \App\Http\Controllers\v1\Recipe\Details::class)->name('details');
    Route::get('/{slug}/ingredients', \App\Http\Controllers\v1\Recipe\Ingredients::class)->name('ingredients.show');
});
