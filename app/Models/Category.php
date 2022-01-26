<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    public $timestamps = false;

    // primary key
    protected $primaryKey = 'category_id';

    public function setPreviewUrlsAttribute($value)
    {
        $array = [];
        foreach ($value as $url) {
            $array[] = $url;
        }
        $this->attributes['preview_urls'] = json_encode($array);
    }

    // getPreviewUrlsAttribute
    public function getPreviewUrlsAttribute($value)
    {
        return json_decode($value);
    }

}
