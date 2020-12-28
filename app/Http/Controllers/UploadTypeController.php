<?php

namespace App\Http\Controllers;

use App\Models\UploadType;

class UploadTypeController extends Controller
{
    function list(UploadType $type)
    {
        return response()->json($type->categories()->latest()->get());
    }
}
