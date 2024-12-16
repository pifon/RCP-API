<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (){
    return response()->json([
        "message" => "Hello API!",
        200
    ]);
});

Route::post('/login',[AuthController::class, 'login']);
Route::post('/register',[AuthController::class, 'register']);
// Route::post('/register', 'AuthController@register');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
