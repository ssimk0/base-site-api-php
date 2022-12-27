<?php

namespace App\Http\Controllers;

use App\Logging\Logger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Logger;

    protected function successResponse(array $data = [], int $status = 200): JsonResponse
    {
        return response()->json(array_merge([
            'success' => true,
        ], $data), $status);
    }

    protected function createSlug($model, $title) {
      $count = $model->count('slug', 'LIKE', $title . '%');

      if ($count > 0) {
        return Str::slug($title . "-$count");
      }

      return Str::slug($title);
    }
}
