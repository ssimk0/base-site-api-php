<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Cocur\Slugify\Slugify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function list(Request $request): JsonResponse {
        $size = $request->query("s");
        $size = intval($size);
        $paginator = Article::where("published", true)->paginate($size, ['*'], "p");

        return response()->json([
            "articles" => $paginator->items(),
            "page" => $paginator->currentPage(),
            "page_size" => $paginator->perPage(),
            "total" => $paginator->total(),
            "total_pages" => $paginator->lastPage()
        ]);
    }

    public function detail(Article $article): JsonResponse {
        if (!$article->published) return response()->json([], 404);

        return response()->json($article);
    }

    public function create(Request $request, Slugify $slugify): JsonResponse {
        $data = $request->validate([
            "title" => "required|min:3|max:255",
            "body" => "required|min:3",
            "short" => "required|min:3",
            "image" => "url",
            "published" => "boolean",
        ]);

        $article = new Article($data);
        $article->user_id = auth()->user()->id;
        $article->slug =  $slugify->slugify($data["title"]);
        $article->save();

        return $this->successResponse($article->toArray(), 201);
    }


    public function update(Request $request, Article $article, Slugify $slugify): JsonResponse {
        $data = $request->validate([
            "title" => "required|min:3|max:255",
            "body" => "required|min:3",
            "short" => "required|min:3",
            "image" => "url",
            "published" => "boolean",
        ]);

        $data["slug"] =  $slugify->slugify($data["title"]);
        $article->update($data);

        return $this->successResponse($article->toArray());
    }

    public function delete(Article $article): JsonResponse
    {
        $article->delete();

        return $this->successResponse();
    }
}
