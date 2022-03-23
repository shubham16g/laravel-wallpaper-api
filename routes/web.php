<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/id/{id}', function ($id) {
    $wall = \App\Models\Wall::find($id);
    $image =  $wall->urls->regular ?? $wall->urls->small;
    $title = 'Wallpaper #' . $id;
    $description = 'Best Wallpaper App Ever';
    $data = compact('image', 'title', 'description');
    return view('wallpaper', $data);
});
