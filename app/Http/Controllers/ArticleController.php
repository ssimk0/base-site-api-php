<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function list(ArticleCategory $category, Request $request): JsonResponse {
        $request->validate([
           'createdBefore' => 'nullable|date_format:Y-m-d',
           'createdAfter' => 'nullable|date_format:Y-m-d',
           's' => 'nullable|integer'
        ]);
        $size = $request->query("s");
        $size = intval($size);
        $createdBefore = $request->query("createdBefore", false);
        $createdAfter = $request->query("createdAfter", false);
        $query = $category->articles()->with('uploads')->where("published", true)->latest();

        if ($createdBefore) {
            $query->whereDate('created_at', '<=', Carbon::parse($createdBefore)->format('Y-m-d'));
        }

        if ($createdAfter) {
            $query->whereDate('created_at', '>=',  Carbon::parse($createdAfter)->format('Y-m-d'));
        }

        $paginator = $query->paginate($size, ['*'], "p");
        return response()->json([
            "data" => $paginator->items(),
            "page" => $paginator->currentPage(),
            "page_size" => $paginator->perPage(),
            "total" => $paginator->total(),
            "total_pages" => $paginator->lastPage()
        ]);
    }


    public function detail(ArticleCategory $category, Article $article): JsonResponse {
        if (!$article->published || $category->id != $article->article_category_id) return response()->json([], 404);
        $article->update(["viewed" => $article->viewed+1]);
        return response()->json($article->load('uploads'));
    }

    public function create(ArticleCategory $category, Request $request): JsonResponse {
        $data = $request->validate([
            "title" => "required|min:3|max:255",
            "body" => "required|min:3",
            "short" => "required|min:3",
            "image" => "url",
            "published" => "boolean",
        ]);

        $dateData = $request->validate([
            "date" => "date"
        ]);

        $uploadData = $request->validate(["uploads.*" => "exists:App\Models\Upload,id"]);

        $article = new Article($data);
        $article->user_id = auth()->user()->id;
        $article->slug =  $this->createSlug($article, $data["title"]);
        $article->article_category_id = $category->id;
        if (Arr::get($dateData, "date", false)) {
            $article->created_at = $dateData["date"];
        }
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
        $dateData = $request->validate([
            "date" => "date"
        ]);

        $uploadData = $request->validate(["uploads.*" => "exists:App\Models\Upload,id"]);

        $data["slug"] = $this->createSlug($article, $data["title"]);
        $article->article_category_id = $category->id;
        if (Arr::get($dateData, "date", false)) {
            $article->created_at = $dateData["date"];
        }

        $article->update($data);
        if (Arr::exists($request->all(), 'uploads')) {
          $uploads = Arr::get($uploadData, 'uploads', []);
          if ($uploads && count($uploads) > 0) {
              $article->uploads()->sync($uploadData["uploads"]);
          } else {
            $article->uploads()->detach();
          }
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
