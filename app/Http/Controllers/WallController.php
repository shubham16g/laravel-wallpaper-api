<?php

namespace App\Http\Controllers;

use App\Models\Wall;
use Illuminate\Http\Request;

class WallController extends Controller
{


    public function index()
    {
        $walls = Wall::all();
        return $walls;
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'color' => 'required|string|max:10',

            'tags' => 'required|array',
            'tags.*' => 'required|string|max:50',

            'urls' => 'required|array',
            'urls.*.full' => 'required|string|max:255',
            'urls.*.small' => 'required|string|max:255',
            'urls.*.raw' => 'nullable|string|max:255',
            'urls.*.regular' => 'nullable|string|max:255',

            'categories' => 'required|array',
            'categories.*' => 'required|integer|exists:categories,category_id',

            'license' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:100',
            'coins' => 'nullable|integer',
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
        if (isset($data['coins'])) {
            $wall->coins = $data['coins'];
        }
        $wall->previewUrls = $data['previewUrls'];
        $wall->save();

        return $wall;
    }

}
