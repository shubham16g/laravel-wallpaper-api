<?php

namespace App\Http\Controllers;

use App\Models\AllTag;
use App\Models\AllWallTag;
use App\Models\Author;
use App\Models\Wall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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

        $walls = Wall::with('allTags')->with('author')->leftJoin('all_wall_tags', 'all_wall_tags.wall_id', '=', 'walls.wall_id')
            ->join('all_tags', 'all_tags.all_tag_id', '=', 'all_wall_tags.all_tag_id')
            ->select('walls.*')
            ->groupBy('all_wall_tags.wall_id')
            ->orderBy(DB::raw('COUNT(all_wall_tags.all_tag_id)'), 'desc');


        if ($category != null && strlen($category) > 2) {

            $walls->orWhere(function ($query) use ($category) {
                $query->where('all_tags.name', '=', "$category")->where('all_tags.type', '=', "category");
            });
        }
        if ($color != null && strlen($color) > 2) {

            $walls->orWhere(function ($query) use ($color) {
                $query->where('all_tags.name', '=', "$color")->where('all_tags.type', '=', "color");
            });
        }

        if ($s != null && strlen($s) > 0) {
            $orderByArray = [];
            $subQueries = explode(' ', $s, 5);

            for ($i = 0; $i < count($subQueries); $i++) {
                $sq = $subQueries[$i];
                $walls->orWhere('all_tags.name', 'like', "%$sq%");
                $orderByArray[0][] = " when all_tags.name LIKE '$sq'  then ";
                $orderByArray[1][] = " when all_tags.name LIKE '$sq%'  then ";
                $orderByArray[2][] = " when all_tags.name LIKE '%$sq%'  then ";
            }

            // check if orderByArray is not empty
            if (sizeof($orderByArray) > 0) {

                $orderByString = '';
                $counter = 1;
                foreach ($orderByArray as $value) {
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

    private function filter(array $response)
    {
        foreach ($response['data'] as $key => $wall) {
            if (isset($wall['all_tags'])) {

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

            'rotation' => 'nullable|integer|min:0|max:360',
            'flip' => 'nullable|in:h,v,hv',

            'author' => 'nullable|array',
            'author.user_name' => 'required_with:author|max:100',
            'author.name' => 'required_with:author|max:100',
            'author.url' => 'nullable|max:255|url',
            'author.image' => 'nullable|max:255',

            'coins' => 'nullable|integer',
        ]);

        $allTags = [];

        foreach ($data['categories'] as $category) {
            $allTags[AllTag::firstOrCreate(['name' => $category, 'type' => 'category'])->all_tag_id] = true;
        }

        foreach ($data['colors'] as $color) {
            $allTags[AllTag::firstOrCreate(['name' => $color, 'type' => 'color'])->all_tag_id] = true;
        }

        foreach ($data['tags'] as $tag) {
            $allTags[$this->findOrCreate($tag, 'tag')] = true;
        }

        $authorId = null;
        if (isset($data['author']['user_name']) && $data['author']['user_name'] != null) {
            $authorId = $this->findOrCreateAuthor($data['author']);
        }

        $wall = new Wall();
        $wall->source = $data['source'];
        $wall->color = $data['color'];
        $wall->urls = $data['urls'];
        if (isset($data['license']) && $data['license'] != null && strlen($data['license']) > 0) {
            $wall->license = $data['license'];
        }
        $wall->author_id = $authorId;
        if (isset($data['coins']) && $data['coins'] != null) {
            $wall->coins = $data['coins'];
        }
        if (isset($data['rotation']) && $data['rotation'] != null) {
            $wall->rotation = $data['rotation'];
        }
        if (isset($data['flip']) && $data['flip'] != null) {
            $wall->flip = $data['flip'];
        }
        $wall->save();

        AllWallTag::insert(array_map(function ($tag) use ($wall) {
            return ['wall_id' => $wall->wall_id, 'all_tag_id' => $tag];
        }, array_keys($allTags)));

        return response()->json(['message' => 'Wallpaper added successfully']);
    }

    private function findOrCreateAuthor($authorArr)
    {
        $url = isset($authorArr['url']) ? $authorArr['url'] : null;
        $image = isset($authorArr['image']) ? $authorArr['image'] : null;
        $author = Author::where('user_name', $authorArr['user_name'])->first();
        if ($author == null) {
            $author = new Author();
            $author->user_name = $authorArr['user_name'];
            $author->name = $authorArr['name'];
            $author->url = $url;
            $author->image = $image;
            $author->save();
        } else {
            $author->name = $authorArr['name'];
            if ($url != null) {
                $author->url = $url;
            }
            if ($image != null) {
                $author->image = $image;
            }
            $author->save();
        }
        return $author->author_id;
    }

    private function findOrCreate($name, $type = null)
    {
        $allTag = AllTag::where('name', '=', $name)->first();
        if ($allTag == null) {
            if ($type != null) {
                $newAllTag = new AllTag();
                $newAllTag->name = $name;
                $newAllTag->type = $type;
                $newAllTag->save();
                return $newAllTag->all_tag_id;
            }
            return null;
        }
        return $allTag->all_tag_id;
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

    public function validateList(Request $request)
    {
        $request->validate([
            'sources' => 'required|array',
            'sources.*' => 'required|max:255|string|url',
        ]);

        // check soureces in wallmodel
        $sources = $request->sources;
        $responseArr = [];
        foreach ($sources as $source) {
            $wall = Wall::where('source', $source)->first();
            $responseArr[] = $wall != null;
        }
        return response()->json($responseArr);
    }
}
