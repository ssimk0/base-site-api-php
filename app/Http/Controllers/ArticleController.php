<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function list(ArticleCategory $category, Request $request): JsonResponse {
        $size = $request->query("s");
        $size = intval($size);
        $paginator = $category->articles()->where("published", true)->latest()->paginate($size, ['*'], "p");

        return response()->json([
            "articles" => $paginator->items(),
            "page" => $paginator->currentPage(),
            "page_size" => $paginator->perPage(),
            "total" => $paginator->total(),
            "total_pages" => $paginator->lastPage()
        ]);
    }


    public function detail(ArticleCategory $category, Article $article): JsonResponse {
        if (!$article->published || $category->id != $article->article_category_id) return response()->json([], 404);
        $article->update(["viewed" => $article->viewed+1]);
        return response()->json($article);
    }

    public function create(ArticleCategory $category, Request $request): JsonResponse {
        $data = $request->validate([
            "title" => "required|min:3|max:255",
            "body" => "required|min:3",
            "short" => "required|min:3",
            "image" => "url",
            "published" => "boolean",
        ]);
        $uploadData = $request->validate(["uploads.*" => "exists:App\Models\Upload,id"]);

        $article = new Article($data);
        $article->user_id = auth()->user()->id;
        $article->slug =  Str::slug($data["title"]);
        $article->article_category_id = $category->id;
        $article->save();

        if (Arr::get($uploadData, 'uploads', false)) {
            $article->uploads()->sync($uploadData["uploads"]);
        }
        return $this->successResponse($article->toArray(), 201);
    }


    public function update(ArticleCategory $category, Request $request, Article $article): JsonResponse {
        $data = $request->validate([
            "title" => "required|min:3|max:255",
            "body" => "required|min:3",
            "short" => "required|min:3",
            "image" => "url",
            "published" => "boolean",
        ]);
        $uploadData = $request->validate(["uploads.*" => "exists:App\Models\Upload,id"]);

        $data["slug"] =  Str::slug($data["title"]);
        $article->article_category_id = $category->id;
        $article->update($data);

        if (Arr::get($uploadData, 'uploads', false)) {
            $article->uploads()->sync($uploadData["uploads"]);
        }

        return $this->successResponse($article->toArray());
    }

    public function delete(ArticleCategory $category, Article $article): JsonResponse
    {
        if ($category->id != $article->article_category_id) {
            return response()->json([], 404);
        }
        $article->delete();

        return $this->successResponse();
    }
}
