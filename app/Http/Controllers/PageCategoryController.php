<?php

namespace App\Http\Controllers;

use App\Models\PageCategory;
use Illuminate\Http\JsonResponse;

class PageCategoryController extends Controller
{
    public function list(): JsonResponse {
        return response()->json(PageCategory::all());
    }
}
