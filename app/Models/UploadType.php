<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadType extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function categories()
    {
        return $this->hasMany(UploadCategory::class, 'type_id');
    }
}
