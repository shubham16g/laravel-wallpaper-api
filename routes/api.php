<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WallController;
use Illuminate\Support\Facades\Route;


/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::prefix('/category')->group(function () {
    // route to get all walls
    Route::get('/', [CategoryController::class, 'index']);

    // route to add new wall
    Route::post('/', [CategoryController::class, 'store']);
});


// RoutePrefix wall
Route::prefix('/wall')->group(function () {
    // route to get all walls
    Route::get('/', [WallController::class, 'index']);
    Route::get('/{id}', [WallController::class, 'category']);

    // route to add new wall
    Route::post('/', [WallController::class, 'store']);
});

