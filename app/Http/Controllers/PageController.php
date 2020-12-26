<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function list(PageCategory $category) {
        $pages = $category->pages->where('page_id', null)->flatten();
        return response()->json($pages);
    }

    public function detail(PageCategory $category, Page $page) {

        if($category->id != $page->page_category->id) {
            return response()->json([], 404);
        }

        $pageArray = $page->toArray();

        $pageArray["children"] = $page->children();
        $pageArray["parent"] = $page->parent();

        return response()->json($pageArray);
    }
}
