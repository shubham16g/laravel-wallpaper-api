<?php

use App\Models\Category;
use App\Models\Wall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

// route to add new category
Route::post('/category', function (Request $request) {
    // validate request
    $request->validate([
        'name' => 'required|string|max:100',
        // 'previewUrls' => 'required|array',
        // 'previewUrls.*' => 'required|string|max:255',
    ]);

    $category = new Category();
    $category->name = $request->name;
    $category->previewUrls = $request->previewUrls;
    // $category->save();
    return $category;
});


Route::post('/wall', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'tags' => 'required|string|max:255',
        'source' => 'required|string|max:255',
        'color' => 'required|string|max:10',
        'urls' => 'nullable|json',
        'categories' => 'nullable|json',
        'license' => 'nullable|string|max:255',
        'author' => 'nullable|string|max:100',
        'downloads' => 'required|integer',
        'coins' => 'required|integer',
        'previewUrls' => 'nullable|json',
    ]);

    $wall = new Wall();
    $wall->name = $data['name'];
    $wall->tags = $data['tags'];
    $wall->source = $data['source'];
    $wall->color = $data['color'];
    $wall->urls = $data['urls'];
    $wall->categories = $data['categories'];
    $wall->license = $data['license'];
    $wall->author = $data['author'];
    $wall->downloads = $data['downloads'];
    $wall->coins = $data['coins'];
    $wall->previewUrls = $data['previewUrls'];
    $wall->save();

    return redirect()->route('walls.index');
});
