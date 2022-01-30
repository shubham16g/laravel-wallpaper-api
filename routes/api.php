<?php

use App\Http\Controllers\AllTagController;
use App\Http\Controllers\WallController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/add/{type}', [AllTagController::class, 'store']);


Route::get('/category', [AllTagController::class, 'index']);

// route postfix group
Route::prefix('/wall')->group(function () {
    Route::get('/', [WallController::class, 'index']);
    Route::post('/', [WallController::class, 'store']);
    Route::delete('/{id}', [WallController::class, 'destroy']);
    Route::get('/download/{id}', [WallController::class, 'download']);
});
Route::get('/{category}/wall', [WallController::class, 'index']);
