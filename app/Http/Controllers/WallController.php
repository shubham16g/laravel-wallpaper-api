<?php

namespace App\Http\Controllers;

use App\Models\AllTag;
use App\Models\AllWallTag;
use App\Models\Wall;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class WallController extends Controller
{


    public function index(Request $request)
    {

        $color = $request->color;
        $category = $request->category;
        $s = $request->s;

        $request->validate([
            'order_by' => 'nullable|in:downloads,newest',
        ]);

        $walls = Wall::with('allTags');

        if ($category != null && strlen($category) > 2) {

            $walls->whereHas('allTags', function ($query) use ($category) {
                $query->where('name', '=', $category)->where('type', '=', "category");
            });
        }
        if ($color != null && strlen($color) > 2) {
            $walls->whereHas('allTags', function ($query) use ($color) {
                $query->where('name', '=', $color)->where('type', '=', "color");
            });
        }

        if ($s != null && strlen($s) > 0) {
            global $orderByArray;
            $orderByArray = [];
            $walls->whereHas('allTags', function ($query) use ($s) {
                $subQueries = explode(' ', $s, 5);
                for ($i = 0; $i < count($subQueries); $i++) {
                    $sq = $subQueries[$i];
                    if ($i == 0) {
                        $query->where('name', 'like', "%$sq%");
                    } else {
                        $query->orWhere('name', 'like', "%$sq%");
                    }
                    $orderByArray[] =
                        [
                            " when tags LIKE '$sq'  then ",
                            " when tags LIKE '$sq%'  then ",
                            " when tags LIKE '%$sq%'  then "
                        ];
                }
            });
            if (!isEmpty($orderByArray)) {

                $orderByArr = array_map(null, ...$orderByArray);
                $orderByString = '';
                $counter = 1;
                foreach ($orderByArr as $value) {
                    foreach ($value as $str) {
                        $orderByString .= $str . ' ' . $counter++;
                    }
                }
                $walls->orderByRaw("case " . $orderByString . " else $counter end");
            }

        }

        if ($request->order_by == 'downloads') {
            $walls->orderBy('downloads', "DESC");
        }
        $walls->orderBy('created_at', "DESC");
        return $this->filter(((object)$walls->paginate())->toArray());
    }

    private function filter(array $response) {
        foreach ($response['data'] as $key => $wall) {
            $response['data'][$key]['colors'] = [];
            $response['data'][$key]['tags'] = [];
            $response['data'][$key]['categories'] = [];
            foreach ($wall['all_tags'] as $tag) {
                if ($tag['type'] == 'category') {
                    $response['data'][$key]['categories'][] = $tag['name'];
                } elseif ($tag['type'] == 'color') {
                    $response['data'][$key]['colors'][] = $tag['name'];
                } else {
                    $response['data'][$key]['tags'][] = $tag['name'];
                }
            }
            unset($response['data'][$key]['all_tags']);
        }
        unset($response['links']);
        unset($response['first_page_url']);
        unset($response['last_page_url']);
        unset($response['next_page_url']);
        unset($response['prev_page_url']);
        unset($response['path']);
        return $response;
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'source' => 'required|max:255|unique:walls',
            'color' => 'required|max:10',

            'urls' => 'required|array',
            'urls.full' => 'required|max:255',
            'urls.small' => 'required|max:255',
            'urls.raw' => 'nullable|max:255',
            'urls.regular' => 'nullable|max:255',


            'tags' => 'required|array',
            'tags.*' => 'required|max:50',

            'categories' => 'required|array',
            'categories.*' => 'required|string|exists:all_tags,name,type,category',

            'colors' => 'required|array',
            'colors.*' => 'required|string|exists:all_tags,name,type,color',

            'license' => 'nullable|max:255',
            'author' => 'nullable|max:100',
            'author_portfolio' => 'nullable|max:255',
            'author_image' => 'nullable|max:255',
            'coins' => 'nullable|integer',
        ]);

        $allTags = [];

        foreach ($data['tags'] as $tag) {
            $allTags[] = AllTag::firstOrCreate(['name' => $tag, 'type' => 'tag'])->all_tag_id;
        }

        foreach ($data['categories'] as $category) {
            $allTags[] = AllTag::firstOrCreate(['name' => $category, 'type' => 'category'])->all_tag_id;
        }

        foreach ($data['colors'] as $color) {
            $allTags[] = AllTag::firstOrCreate(['name' => $color, 'type' => 'color'])->all_tag_id;
        }


        $wall = new Wall();
        $wall->source = $data['source'];
        $wall->color = $data['color'];
        $wall->urls = $data['urls'];
        $wall->license = $data['license'];
        $wall->author = $data['author'];
        $wall->author_portfolio = $data['author_portfolio'];
        $wall->author_image = $data['author_image'];
        if (isset($data['coins'])) {
            $wall->coins = $data['coins'];
        }
        $wall->save();

        AllWallTag::insert(array_map(function ($tag) use ($wall) {
            return ['wall_id' => $wall->wall_id, 'all_tag_id' => $tag];
        }, $allTags));

        return response()->json(['message' => 'Wallpaper added successfully']);
    }

    // delete wallpaper
    public function destroy($id)
    {
        $wall = Wall::find($id);
        if ($wall != null) {
            $wall->delete();
            return response()->json(['message' => 'Wallpaper deleted successfully']);
        }
        return response()->json(['message' => 'No Wallpaper found with given id'], 404);
    }

    public function download($id)
    {
        $wall = Wall::where('id', $id)->increment('downloads');
        if ($wall) {
            return response()->json(['message' => 'Wallpaper downloaded successfully']);
        }
        return response()->json(['message' => 'No Wallpaper found with given id'], 404);
    }
}
