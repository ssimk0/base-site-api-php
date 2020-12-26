<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageCategory extends Model
{
    use HasFactory;

    public function pages() {
        return $this->hasMany(Page::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
