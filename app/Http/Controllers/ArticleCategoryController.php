<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ArticleCategoryController extends Controller
{

    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            "name" => "required|min:3|max:255",
            "lang" => "max:255"
        ]);

        $article = new ArticleCategory($data);
        $article->slug = Str::slug($data["name"]);
        $article->save();

        return $this->successResponse($article->toArray(), 201);
    }

    public function list(Request $request): JsonResponse
    {
        $data = $request->validate([
            "lang" => "string"
        ]);
        if (Arr::get($data, "lang", false)) {
            $categories = ArticleCategory::where("lang", $data["lang"])->get();
        } else {
            $categories = ArticleCategory::all();
        }

        return response()->json($categories);
    }

    public function update(Request $request, ArticleCategory $category): JsonResponse
    {
        $data = $request->validate([
            "name" => "required|min:3|max:255",
        ]);

        $data["slug"] = Str::slug($data["name"]);
        $category->update($data);

        return $this->successResponse($category->toArray());
    }

    public function delete(ArticleCategory $category): JsonResponse
    {
        $category->delete();

        return $this->successResponse();
    }
}
