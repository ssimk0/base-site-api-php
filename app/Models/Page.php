<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $with = ["page_category"];

    public function page_category() {
        return $this->belongsTo(PageCategory::class, 'page_category_id');
    }

    public function children() {
        return Page::where('page_id', $this->id)->get("*");
    }

    public function parent() {
        if ($this->page_id) {
            return Page::where('id', $this->page_id)->get();
        }

        return $this;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
