<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Island;
use Illuminate\Http\Request;

class IslandController extends Controller
{
    public function IslandListAll()
    {
        $islands = Island::all();

        $data = $islands->map(function ($island) {
            return [
                'id' => $island->id,
                'name' => $island->name,
                'image' => $island->image ? asset('storage/' . ltrim($island->image, '/')) : null,
                'price' => $island->price,
                'description' => $island->description,
                'created_at' => $island->created_at?->toDateTimeString(),
                'updated_at' => $island->updated_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
