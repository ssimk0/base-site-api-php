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

        return response()->json($page->with(['parent', 'children'])->where("id", $page->id)->first());
    }
    // TODO: finish create
    public function create(Request $request) {

    }
    // TODO: finish update
    public function update(Page $page, Request $request) {

    }

    public function delete(Page $page) {
        try {
            $page->delete();
        } catch (\Exception $e) {
            $this->logCritical("problem with delete page: " . $e->getMessage());
        }
        return response()->json();
    }
}
