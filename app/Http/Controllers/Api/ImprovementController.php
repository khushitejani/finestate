<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Improvement;
use Illuminate\Http\Request;

class ImprovementController extends Controller
{
    public function ImprovementListAll()
    {
        $improvements = Improvement::all();

        $data = $improvements->map(function ($improvement) {
            return [
                'id' => $improvement->id,
                'name' => $improvement->name,
                'image' => !empty($improvement->image) ? asset('storage/' . ltrim($improvement->image, '/')) : null,
                'price' => $improvement->price,
                'created_at' => $improvement->created_at ? $improvement->created_at->toDateTimeString() : null,
                'updated_at' => $improvement->updated_at ? $improvement->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
