<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Construction;
use Illuminate\Http\Request;

class ConstructionController extends Controller
{
    public function ConstructionListAll()
    {
        $constructions = Construction::all();

        $data = $constructions->map(function ($construction) {

            return [
                'id' => $construction->id,
                'no' => $construction->no,
                'name' => $construction->name,
                'metal' => $construction->metal,
                'builder_men' => $construction->builder_men,
                'wood' => $construction->wood,
                'concrete' => $construction->concrete,
                'total_cost_of_construction' => $construction->total_cost_of_construction,
                'total_profit' => $construction->total_profit,
                'total_percentage' => $construction->total_percentage,
                'time' => $construction->time,

                'image' => !empty($construction->image)
                    ? asset('storage/' . ltrim($construction->image, '/'))
                    : null,

                'total_return_after_completion' => $construction->total_return_after_completion,
                'created_at' => $construction->created_at ? $construction->created_at->toDateTimeString() : null,
                'updated_at' => $construction->updated_at ? $construction->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
