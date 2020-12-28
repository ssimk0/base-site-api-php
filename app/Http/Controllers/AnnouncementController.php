<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function active()
    {
        $a = Announcement::whereDate("expire_at", ">=", Carbon::now())->latest()->first();

        return response()->json($a);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "expire_at" => "date|after_or_equal:today|required",
            "message" => "required|min:3|max:255"
        ]);

        $a = Announcement::create($data);

        return $this->successResponse($a->toArray(), 201);
    }
}
