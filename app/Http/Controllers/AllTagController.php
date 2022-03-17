<?php

namespace App\Http\Controllers;

use App\Models\AllTag;
use App\Models\Wall;
use Illuminate\Http\Request;

class AllTagController extends Controller
{

    public function init(Request $request)
    {
        return [
            'colors' => $this->index($request, 'color'),
            'categories' => $this->index($request, 'category'),
        ];
    }

    public function index(Request $request, $type)
    {
        $request->merge(['type' => $type]);
        $request->validate([
            'type' => 'required|string|max:100|in:category,color',
        ]);
        $columns = ['name', 'popularity', 'value'];

        $categories = AllTag::where('type', $type)->orderBy('popularity')->orderBy('name')->get($columns);
        if ($request->type == 'category') {
            foreach ($categories as $category) {
                $category->image = $category->value;
                unset($category->value);
            }
        }

        return $categories;
    }

    public function store(Request $request, $type)
    {
        $request->merge(['type' => $type]);
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|max:100|in:category,color',
            'value' => 'required_if:type,color|string|regex:/^#[0-9a-fA-F]{6}$/',
            'image' => 'required_if:type,category|string|max:255',
            // validate color hex code

        ]);

        $category = AllTag::where('name', $request->name)->first();
        if ($category == null) {
            $category = new AllTag();
        }
        $category->name = $request->name;
        if ($request->type == 'color') {
            $category->value = $request->value;
        } else if ($request->type == 'category') {
            $category->value = $request->image;
        }
        $category->type = $type;
        $category->save();
        return $category;
    }
}
