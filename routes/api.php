<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WallController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/category')->group(function () {
    // route to get all walls
    Route::get('/', [CategoryController::class, 'index']);

    // route to add new wall
    Route::post('/', [CategoryController::class, 'store']);
});

Route::get('/{category}/wall', [WallController::class, 'index']);
Route::get('/wall', [WallController::class, 'index']);
Route::post('/wall', [WallController::class, 'store']);
