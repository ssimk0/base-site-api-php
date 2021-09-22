<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function uploads()
    {
        return $this->belongsToMany(Upload::class, 'article_upload', 'article_id', 'upload_id')->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }
}
