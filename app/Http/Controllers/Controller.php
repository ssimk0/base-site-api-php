<?php

namespace App\Http\Controllers;

use App\Logging\Logger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Logger;

    protected function successResponse($data = [], $status = 200)
    {
        return response()->json(array_merge([
            'success' => true,
        ], $data), $status);
    }
}
