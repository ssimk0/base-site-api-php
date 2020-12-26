<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function list(PageCategory $category) {
        return response()->json($category->pages);
    }

    public function detail(PageCategory $category, Page $page) {

        if($category->id != $page->category->id) {
            return response()->json([], 404);
        }

        return response()->json($page);
    }
}
