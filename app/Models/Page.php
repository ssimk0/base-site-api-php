<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory;
    protected $with = ["page_category"];
    protected $guarded = [];
    protected $hidden = ["page_id", "user_id", "page_category_id"];

    /**
     * @return BelongsTo
     *
     * @psalm-return BelongsTo<PageCategory>
     */
    public function page_category(): BelongsTo {
        return $this->belongsTo(PageCategory::class, 'page_category_id');
    }

    /**
     * @return HasMany
     *
     * @psalm-return HasMany<self>
     */
    public function children(): HasMany {
        return $this->hasMany(Page::class, 'page_id');
    }

    /**
     * @return BelongsTo
     *
     * @psalm-return BelongsTo<self>
     */
    public function parent(): BelongsTo {
        return $this->belongsTo(Page::class, 'page_id')->with("children");
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
