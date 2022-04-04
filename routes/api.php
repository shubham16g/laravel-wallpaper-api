<?php

use App\Http\Controllers\AllTagController;
use App\Http\Controllers\WallController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.user')->group(function () {
    Route::get('/wall/download/{id}', [WallController::class, 'download']);
    Route::post('/wall/list/', [WallController::class, 'list']);
    Route::middleware('cache.headers:public;max_age=43200')->get('/featured', [WallController::class, 'featured']);
    Route::middleware('cache.headers:public;max_age=43200')->get('/list/{type}', [AllTagController::class, 'index']);
    Route::middleware('cache.headers:public;max_age=43200')->get('/init', [AllTagController::class, 'init']);
    Route::middleware('cache.headers:public;max_age=900')->get('/wall/', [WallController::class, 'index']);
});

Route::middleware('api.admin')->group(function () {
    Route::post('/add/{type}', [AllTagController::class, 'store']);
    Route::post('/wall/', [WallController::class, 'store']);
    Route::post('/wall/validate', [WallController::class, 'validateList']);
    Route::delete('/wall/{id}', [WallController::class, 'destroy']);
});
