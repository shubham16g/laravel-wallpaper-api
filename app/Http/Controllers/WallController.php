<?php

namespace App\Http\Controllers;

use App\Models\AllTag;
use App\Models\AllWallTag;
use App\Models\Author;
use App\Models\Base;
use App\Models\Wall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class WallController extends Controller
{

    public function index(Request $request)
    {

        $color = $request->color;
        $category = $request->category;
        $perPage = $request->per_page;
        $s = $request->s;

        $request->validate([
            'order_by' => 'nullable|in:downloads,newest',
        ]);

        $walls = Wall::with('allTags')->with('author')->leftJoin('all_wall_tags', 'all_wall_tags.wall_id', '=', 'walls.wall_id')
            ->join('all_tags', 'all_tags.all_tag_id', '=', 'all_wall_tags.all_tag_id')
            ->select('walls.*')
            ->groupBy('all_wall_tags.wall_id');

        if ($category != null || $s != null || $color != null) {
            $walls->orderBy(DB::raw('COUNT(all_wall_tags.all_tag_id)'), 'desc');
        }


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
        if ($perPage == null) {
            $perPage = 18;
        }
        return $this->filterPagination(((object)$walls->paginate($perPage))->toArray());
    }

    private function filterPagination(array $response)
    {
        $this->filterWallList($response['data']);
        unset($response['links']);
        unset($response['first_page_url']);
        unset($response['last_page_url']);
        unset($response['next_page_url']);
        unset($response['prev_page_url']);
        unset($response['path']);
        return $response;
    }

    private function filterWallList(array &$data)
    {
        foreach ($data as $key => $wall) {
            if (isset($wall['all_tags'])) {

                $data[$key]['colors'] = [];
                $data[$key]['tags'] = [];
                $data[$key]['categories'] = [];

                foreach ($wall['all_tags'] as $tag) {
                    if ($tag['type'] == 'category') {
                        $data[$key]['categories'][] = $tag['name'];
                    } elseif ($tag['type'] == 'color') {
                        $data[$key]['colors'][] = $tag['name'];
                    } else {
                        $data[$key]['tags'][] = $tag['name'];
                    }
                }
                unset($data[$key]['all_tags']);
            }
        }
    }

    public function list(Request $request)
    {
        $request->validate([
            'list' => 'required|array',
            'list.*' => 'required|integer',
        ]);

        $walls = Wall::with('allTags')->with('author')->leftJoin('all_wall_tags', 'all_wall_tags.wall_id', '=', 'walls.wall_id')
            ->join('all_tags', 'all_tags.all_tag_id', '=', 'all_wall_tags.all_tag_id')
            ->select('walls.*')
            ->groupBy('all_wall_tags.wall_id');
        $walls->orderBy(DB::raw('COUNT(all_wall_tags.all_tag_id)'), 'desc');
        $walls->whereIn('walls.wall_id', $request->list);
        return $this->filterPagination(((object)$walls->paginate(18))->toArray());
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
        $wall = Wall::find($id);
        if ($wall != null) {
            $wall->increment('downloads');
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


    /** ************* Featured*********************************************************/
    public function base()
    {
        // return Featured::select('wall_id')->get();
        $baseData = Base::find(1);
        if ($baseData == null) {
            return response()->json(['message' => 'Base Data not found'], 404);
        }
        $walls = Wall::with('allTags')->with('author')->leftJoin('all_wall_tags', 'all_wall_tags.wall_id', '=', 'walls.wall_id')
            ->join('all_tags', 'all_tags.all_tag_id', '=', 'all_wall_tags.all_tag_id')
            ->select('walls.*')
            ->groupBy('all_wall_tags.wall_id');
        $walls->where('walls.wall_id', $baseData->featured);
        $data = $walls->get()->toArray();
        $this->filterWallList($data);
        $baseData->featured = $data[0];
        unset($baseData->id);
        return $baseData;
    }

    public function baseUpdate(Request $request)
    {
        // may be required to add nullable in some fields
        $request->validate([
            'featured' => 'integer|exists:walls,wall_id',
            'feature_title' => 'string|max:255',
            'feature_description' => 'string|max:255',
            'current_version' => 'integer|min:1',
            'immediate_update' => 'integer|min:1',
            'play_store_url_short' => 'string|max:255',

        ]);
        $baseData = Base::find(1);
        if ($baseData == null) {
            $baseData = new Base();
        }
        if ($request->has('featured')) {
            $baseData->featured = $request->featured;
        }
        if ($request->has('feature_title')) {
            $baseData->feature_title = $request->feature_title;
        }
        if ($request->has('feature_description')) {
            $baseData->feature_description = $request->feature_description;
        }
        if ($request->has('current_version')) {
            $baseData->current_version = $request->current_version;
        }
        if ($request->has('immediate_update')) {
            $baseData->immediate_update = $request->immediate_update;
        }
        if ($request->has('play_store_url_short')) {
            $baseData->play_store_url_short = $request->play_store_url_short;
        }
        $baseData->save();
        return response()->json(['message' => 'Base data updated successfully']);
    }
}
