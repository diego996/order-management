<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class,'login'])
->withoutMiddleware(['auth:sanctum','throttle:api']);;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    //stampami tutte le rotte racchiuse in apiResource
    Route::apiResource('orders',     OrderController::class);
});



// Rotte protette da Sanctum (token):
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',   [AuthController::class,'user']);
    Route::post('/logout',[AuthController::class,'logout']);
});