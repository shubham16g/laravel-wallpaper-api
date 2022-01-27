<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wall extends Model
{
    use HasFactory;

    protected $perPage = 16;

    // settagsAttribute
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
}
