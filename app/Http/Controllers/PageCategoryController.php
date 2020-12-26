<?php

namespace App\Http\Controllers;

use App\Models\PageCategory;

class PageCategoryController extends Controller
{
    public function list() {
        return response()->json(PageCategory::all());
    }
}
