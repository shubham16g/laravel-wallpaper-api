<?php

namespace App\Http\Controllers;

use App\Models\Wall;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class WallController extends Controller
{


    public function index(Request $request, $category = null)
    {
        $category = strtolower($category);

        $walls = Wall::query();

        if ($category != null) {
            $walls->whereRaw("JSON_CONTAINS(lower(JSON_EXTRACT(categories, '$')), '\"{$category}\"')");
        }

        if ($request->has('s')) {

            $query = strtolower($request->s);

            $orderByString = "case
                when name LIKE '$query%' then 1
                when tags LIKE '\"$query%' then 2
                when categories LIKE '%$query%' then 3
                when name LIKE '%$query%' then 4
                when tags LIKE '%$query%' then 5 ";
            $counter = 6;

            global $orderByArray;
            $orderByArray = [];
            $walls->where(function ($whereQuery) use ($query) {

                global $orderByArray;
                $whereQuery->where('name', 'like', '%' . $query . '%');
                $whereQuery->orWhere('categories', 'like', '%' . $query . '%');
                $whereQuery->orWhere('tags', 'like', '%' . $query . '%');

                $subQueries = explode(' ', $query, 3);

                foreach ($subQueries as $q) {
                    $whereQuery->orWhere('name', 'like', '%' . $q . '%');
                    $whereQuery->orWhere('categories', 'like', '%' . $q . '%');
                    $whereQuery->orWhere('tags', 'like', '%' . $q . '%');
                    $orderByArray[] =
                        [
                            " when name LIKE '$q%' then ",
                            " when tags LIKE '\"$q%' then ",
                            " when categories LIKE '%$q%' then ",
                            " when name LIKE '%$q%' then ",
                            " when tags LIKE '%$q%'  then "
                        ];
                }
            });
            if (!isEmpty($orderByArray)) {

                $orderByArr = array_map(null, ...$orderByArray);

                foreach ($orderByArr as $value) {
                    foreach ($value as $str) {
                        $orderByString .= $str . ' ' . $counter++;
                    }
                }
            }

            $walls->orderByRaw($orderByString . " else $counter end");
        }
        return $walls->paginate();
    }

    public function category($category)
    {
        $category = strtolower($category);
        // where json array contains id in categories column
        $walls = Wall::whereRaw("JSON_CONTAINS(lower(JSON_EXTRACT(categories, '$')), '\"{$category}\"')")->paginate();

        return $walls;
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'source' => 'required|max:255',
            'color' => 'required|max:10',

            'tags' => 'required|array',
            'tags.*' => 'required|max:50',

            'urls' => 'required|array',
            'urls.full' => 'required|max:255',
            'urls.small' => 'required|max:255',
            'urls.raw' => 'nullable|max:255',
            'urls.regular' => 'nullable|max:255',

            'categories' => 'required|array',
            'categories.*' => 'required|string|exists:categories,name',

            'license' => 'nullable|max:255',
            'author' => 'nullable|max:100',
            'coins' => 'nullable|integer',
        ]);

        $wall = new Wall();
        $wall->name = $data['name'];
        $wall->source = $data['source'];
        $wall->color = $data['color'];
        $wall->tags = $data['tags'];
        $wall->urls = $data['urls'];
        $wall->categories = $data['categories'];
        $wall->license = $data['license'];
        $wall->author = $data['author'];
        if (isset($data['coins'])) {
            $wall->coins = $data['coins'];
        }
        $wall->save();

        return $wall;
    }
}
