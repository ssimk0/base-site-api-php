<?php

namespace App\Http\Controllers;

use App\Models\UploadCategory;
use App\Models\UploadType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadCategoryController extends Controller
{
    public function list(UploadType $type, UploadCategory $category, Request $request)
    {
        $size = $request->query("s");
        $size = intval($size);
        $paginator = $category->uploads()->latest()->paginate($size, ['*'], 'p');

        return response()->json([
            "upload" => $paginator->items(),
            "page" => $paginator->currentPage(),
            "page_size" => $paginator->perPage(),
            "total" => $paginator->total(),
            "total_pages" => $paginator->lastPage()
        ]);
    }

    public function store(UploadType $type, Request $request)
    {
        $data = $request->validate([
           "name" => 'required|min:3|max:255',
           "description" => 'required|min:3|max:255',
           "subpath" => 'required|min:3|max:255',
        ]);

        $category = new UploadCategory($data);
        $category->type_id = $type->id;
        $category->slug = Str::slug($category->name);
        $category->save();

        return $this->successResponse($category->toArray(), 201);
    }


    public function update(UploadType $type, UploadCategory $category, Request $request)
    {
        $data = $request->validate([
            "description" => 'required|min:3|max:255'
        ]);

        $category->update($data);
        return $this->successResponse();
    }

    public function delete(UploadType $type, UploadCategory $category, Request $request)
    {
        $category->delete();
        return $this->successResponse();
    }
}
