<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wall extends Model
{
    use HasFactory;

    protected $perPage = 16;

    // primary key
    protected $primaryKey = 'wall_id';

    // hidden author_id
    protected $hidden = ['author_id'];

    public function allTags()
    {
        return $this->belongsToMany('App\Models\AllTag', 'all_wall_tags', 'wall_id', 'all_tag_id');
    }

    public function author()
    {
        return $this->hasOne(Author::class, 'author_id', 'author_id');
    }

    // setUrlsAttribute
    public function setUrlsAttribute($value)
    {
        $this->attributes['urls'] = json_encode($value);
    }

    // getUrlsAttribute
    public function getUrlsAttribute($value)
    {
        return json_decode($value);
    }

    /* // settagsAttribute
    public function setTagsAttribute($value)
    {
        $array = [];
        foreach ($value as $tag) {
            $array[] = strtolower($tag);
        }
        $this->attributes['tags'] = json_encode($array);
    }
    // gettagsAttribute
    public function getTagsAttribute($value)
    {
        return json_decode($value);
    }
    // setCategoriesAttribute
    public function setCategoriesAttribute($value)
    {
        $array = [];
        foreach ($value as $category_name) {
            $array[] = strtolower($category_name);
        }
        $this->attributes['categories'] = json_encode($array);
    }

    // getCategoriesAttribute
    public function getCategoriesAttribute($value)
    {
        return json_decode($value);
    }
    */
}
