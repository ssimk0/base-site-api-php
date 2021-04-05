<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageCategory extends Model
{
    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @psalm-return \Illuminate\Database\Eloquent\Relations\HasMany<Page>
     */
    public function pages(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(Page::class)->orderBy("created_at");
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
