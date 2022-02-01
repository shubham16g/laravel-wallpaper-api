<?php

namespace App\Http\Controllers;

use App\Models\AllTag;
use Illuminate\Http\Request;

class AllTagController extends Controller
{

    public function index(Request $request, $type)
    {
        $request->merge(['type' => $type]);
        $request->validate([
            'type' => 'required|string|max:100|in:category,color',
        ]);
        $categories = AllTag::where('type', $type)->get(['name','value', 'popularity'])->sortByDesc('popularity')->toArray();
        return $categories;
    }

    public function store(Request $request, $type)
    {
        $request->merge(['type' => $type]);
        $request->validate([
            'name' => 'required|string|max:100|unique:all_tags,name',
            'type' => 'required|string|max:100|in:category,color',
            'value' => 'required_if:type,color|string|regex:/^#[0-9a-fA-F]{6}$/',
            // validate color hex code

        ]);

        $category = new AllTag();
        $category->name = $request->name;
        if ($request->type == 'color') {
            $category->value = $request->value;
        }
        $category->type = $type;
        $category->save();
        return $category;
    }

}
