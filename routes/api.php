<?php

use App\Http\Controllers\AllTagController;
use App\Http\Controllers\WallController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api.user')->group(function () {
    Route::get('/list/{type}', [AllTagController::class, 'index']);
    Route::get('/init', [AllTagController::class, 'init']);
    Route::get('/wall/', [WallController::class, 'index']);
    Route::get('/wall/download/{id}', [WallController::class, 'download']);
    Route::post('/wall/list/', [WallController::class, 'list']);
});

Route::middleware('api.admin')->group(function () {
    Route::post('/add/{type}', [AllTagController::class, 'store']);
    Route::post('/wall/', [WallController::class, 'store']);
    Route::post('/wall/validate', [WallController::class, 'validateList']);
    Route::delete('/wall/{id}', [WallController::class, 'destroy']);
});
