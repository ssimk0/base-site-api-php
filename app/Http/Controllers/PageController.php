<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class PageController
 * @package App\Http\Controllers
 */
class PageController extends Controller
{
    /**
     * @param PageCategory $category
     * @return JsonResponse
     */
    public function list(PageCategory $category): JsonResponse {
        $pages = $category->pages->where('page_id', null)->flatten();
        return response()->json($pages);
    }

    /**
     * @param PageCategory $category
     * @param Page $page
     * @return JsonResponse
     */
    public function detail(PageCategory $category, Page $page): JsonResponse {

        if($category->id != $page->page_category->id) {
            return response()->json([], 404);
        }

        return response()->json($page->with(['parent', 'children'])->where("id", $page->id)->first());
    }

    /**
     * @param PageCategory $category
     * @param Request $request
     * @return JsonResponse
     */
    public function create(PageCategory $category, Request $request): JsonResponse {
        $data = $request->validate([
            "title" => "string|required|max:255|min:3",
            "body" => "required|min:3",
            "page_id" => "integer",
        ]);

        $page = new Page($data);
        $page->page_category_id = $category->id;
        $page->user_id = auth()->user()->id;
        $page->slug =  Str::slug($page->title);
        $page->save();

        return $this->successResponse($page->toArray(), 201);
    }

    /**
     * @param Request $request
     * @param PageCategory|null $category
     * @param Page $page
     * @return JsonResponse
     */
    public function update(Request $request, ?PageCategory $category, Page $page): JsonResponse
    {
        $data = $request->validate([
            "title" => "required|string|max:255|min:3",
            "body" => "required|min:3"
        ]);

        $data["slug"] = Str::slug($data["title"]);
        $page->update($data);

        return $this->successResponse();
    }

    /**
     * @param Page $page
     * @return JsonResponse
     * @throws \Exception
     */
    public function delete(?PageCategory $category, Page $page): JsonResponse {
        $page->delete();

        return $this->successResponse();
    }
}
