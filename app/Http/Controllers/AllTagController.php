<?php

namespace App\Http\Controllers;

use App\Models\AllTag;
use Illuminate\Http\Request;

class AllTagController extends Controller
{

    public function init(Request $request)
    {
        return [
            'colors'=> $this->index($request, 'color'),
            'category'=> $this->index($request, 'category'),
        ];
    }

    public function index(Request $request, $type)
    {
        $request->merge(['type' => $type]);
        $request->validate([
            'type' => 'required|string|max:100|in:category,color',
        ]);
        $columns = ['name', 'popularity'];
        if ($request->type == 'color') {
            $columns[] = 'value';
        }

        $categories = AllTag::where('type', $type)->orderBy('popularity')->orderBy('name')->get($columns);
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
