<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jewelled;
use Illuminate\Http\Request;

class JewelledController extends Controller
{
    public function JewelledListAll()
    {
        $jewels = Jewelled::all();

        $data = $jewels->map(function ($jewel) {
            return [
                'id' => $jewel->id,
                'name' => $jewel->name,
                'image' => $jewel->image ? asset('storage/' . ltrim($jewel->image, '/')) : null,
                'price' => $jewel->price,
                'created_at' => $jewel->created_at ? $jewel->created_at->toDateTimeString() : null,
                'updated_at' => $jewel->updated_at ? $jewel->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
