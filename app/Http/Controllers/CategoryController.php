<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();
        return $categories;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'preview_urls' => 'required|array',
            'preview_urls.*' => 'required|string|max:255',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->preview_urls = $request->preview_urls;
        $category->save();
        return $category;
    }

}
