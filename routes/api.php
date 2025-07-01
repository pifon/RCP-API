<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Welcome, ping route
Route::get('/', function () {
    return response()->json([
        'message' => sprintf('Welcome to %s API.', ucfirst(config('app.name'))),
        200,
    ]);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
