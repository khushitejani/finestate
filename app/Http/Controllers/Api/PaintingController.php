<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Painting;
use Illuminate\Http\Request;

class PaintingController extends Controller
{
    public function PaintingListAll()
    {
        $paintings = Painting::all();

        $data = $paintings->map(function ($painting) {
            return [
                'id' => $painting->id,
                'name' => $painting->name,
                'years' => $painting->years,
                'image' => $painting->image ? asset('storage/' . ltrim($painting->image, '/')) : null,
                'price' => $painting->price,
                'created_at' => $painting->created_at?->toDateTimeString(),
                'updated_at' => $painting->updated_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
