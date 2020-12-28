<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Models\UploadCategory;
use App\Models\UploadType;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Storage;


class UploadController extends Controller
{
    const SMALL = "resized/small";
	const LARGE = "reduced";


    public function latest(UploadType $type, UploadCategory $category)
    {
        $latest = $category->uploads()->latest()->firstOrFail();

        return $this->download_file($latest);
    }

    public function download(UploadType $type, UploadCategory $category, Upload $upload)
    {
        return $this->download_file($upload);
    }

    public function detail(UploadType $type, UploadCategory $category, Upload $upload)
    {
        return response()->json($upload);
    }

    public function update(UploadType $type, UploadCategory $category, Upload $upload, Request $request)
    {
        $data = $request->validate([
            "description" => 'required|min:3|max:255'
        ]);

        $upload->update($data);

        return $this->successResponse();
    }


    public function delete(UploadType $type, UploadCategory $category, Upload $upload)
    {
        $upload->delete();

        return $this->successResponse();
    }

    public function store(UploadType $type, UploadCategory $category, Request $request)
    {
        $data = $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'string|min:3|max:255'
        ]);

        $images = $this->storeImage($request);
        $typeSlug = $type->slug;
        $categorySlug = $category->slug;

        Storage::put("{$typeSlug}/{$categorySlug}/".self::LARGE."/".$images["filename"], $images["file"]->__toString(), 'public');
        Storage::put("{$typeSlug}/{$categorySlug}/".self::SMALL."/".$images["filename"], $images["thumb"]->__toString(), 'public');

        $upload = new Upload([
            "file" => Storage::url("{$typeSlug}/{$categorySlug}/".self::LARGE."/".$images["filename"]),
            "thumbnail" => Storage::url("{$typeSlug}/{$categorySlug}/".self::SMALL."/".$images["filename"]),
            "description" => Arr::get($data, 'description', ''),
            "category_id" => $category->id,
        ]);

        $upload->save();

        if ($category->thumbnail == null) {
            $category->update(["thumbnail" => $upload->file]);
        }

        return $this->successResponse($upload->toArray(), 201);
    }

    /**
     * Prepares a image for storing.
     *
     * @param mixed $request
     * @author Niklas Fandrich
     */
    protected function storeImage($request)
    {
        // Get file from request
        $file = $request->file('file');

        // Get filename with extension
        $filename = Str::random(15);

        // Get the original image extension
        $extension = $file->getClientOriginalExtension();

        // Create unique file name
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;

        // Refer image to method resizeImage
        return array_merge($this->resizeImage($file, $fileNameToStore), ["filename" => $fileNameToStore]);
    }

    /**
     * Resizes a image using the InterventionImage package.
     *
     * @param object $file
     * @return mixed
     * @author Niklas Fandrich
     */
    protected function resizeImage($file)
    {
        $img = Image::make($file);
        $width = $img->getWidth();
        // Resize image
        $large = $img->resize($width > 1920 ? 1920 : $width, null, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg', 75);

        $thumb = Image::make($file)->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg', 75);

        return ["file" => $large, "thumb" => $thumb];
    }

    protected function download_file(Upload $upload)
    {
        $response = Http::asMultipart()->get($upload->file);
        if ($response->status() != 200) {
            abort(404);
        }
        return response($response->body(), 200, $response->headers());
    }
}
