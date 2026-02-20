<?php

use App\Http\Controllers\AuthController;
use App\JsonApi\Document;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(
        Document::meta([
            'name' => config('app.name').' API',
            'version' => 'v1',
        ]),
        200,
    );
})->name('welcome');

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', \App\Http\Controllers\v1\Auth\Register::class)->name('register');
