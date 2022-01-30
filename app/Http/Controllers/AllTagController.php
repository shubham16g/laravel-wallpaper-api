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
        $categories = AllTag::where('type', $type)->get(['all_tag_id', 'name', 'popularity']);
        return $categories;
    }

    public function store(Request $request, $type)
    {
        $request->merge(['type' => $type]);
        $request->validate([
            'name' => 'required|string|max:100|unique:all_tags,name',
            'type' => 'required|string|max:100|in:category,color',
        ]);

        $category = new AllTag();
        $category->name = $request->name;
        $category->type = $type;
        $category->save();
        return $category;
    }

}
