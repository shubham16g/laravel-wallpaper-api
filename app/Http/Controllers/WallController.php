<?php

namespace App\Http\Controllers;

use App\Models\Wall;
use Illuminate\Http\Request;

class WallController extends Controller
{


    public function index(Request $request)
    {
        if ($request->has('s')) {

            $query = $request->s;

            $orderByString = "case
                when name LIKE '$query%' then 1
                when name LIKE '%$query%'  then 2
                when tags LIKE '\"$query%' then 3
                when tags LIKE '%$query%'  then 4 ";

            $walls = Wall::query();
            $walls->where('name', 'like', '%' . $query . '%');
            $walls->orWhere('tags', 'like', '%' . $query . '%');

            $subQueries = explode(' ', $query, 3);

            $counter = 5;

            foreach ($subQueries as $q) {
                $walls->orWhere('name', 'like', '%' . $q . '%');
                $walls->orWhere('tags', 'like', '%' . $q . '%');
                $orderByString .= " when name LIKE '$q%' then $counter ";
                $counter++;
                $orderByString .= " when name LIKE '%$q%' then $counter ";
                $counter++;
                $orderByString .= " when tags LIKE '\"$q%' then $counter ";
                $counter++;
                $orderByString .= " when tags LIKE '%$q%'  then $counter ";
                $counter++;
            }

            return $walls->orderByRaw($orderByString . " else $counter end")->paginate();
        } else {
            $walls = Wall::paginate();
            return $walls;
        }
    }

    public function category($id)
    {
        // where json array contains id in categories column
        $walls = Wall::whereRaw("JSON_CONTAINS(JSON_EXTRACT(categories, '$'), '{$id}')")->paginate();

        return $walls;
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|query|max:255',
            'source' => 'required|query|max:255',
            'color' => 'required|query|max:10',

            'tags' => 'required|array',
            'tags.*' => 'required|query|max:50',

            'urls' => 'required|array',
            'urls.*.full' => 'required|query|max:255',
            'urls.*.small' => 'required|query|max:255',
            'urls.*.raw' => 'nullable|query|max:255',
            'urls.*.regular' => 'nullable|query|max:255',

            'categories' => 'required|array',
            'categories.*' => 'required|integer|exists:categories,category_id',

            'license' => 'nullable|query|max:255',
            'author' => 'nullable|query|max:100',
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