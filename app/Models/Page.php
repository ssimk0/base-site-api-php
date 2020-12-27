<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $with = ["page_category"];
    protected $hidden = ["page_id", "user_id", "page_category_id"];

    public function page_category() {
        return $this->belongsTo(PageCategory::class, 'page_category_id');
    }

    public function children() {
        return $this->hasMany(Page::class, 'page_id');
    }

    public function parent() {
        return $this->belongsTo(Page::class, 'page_id')->with("children");
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
