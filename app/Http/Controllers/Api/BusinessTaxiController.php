<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BusinessTaxi;
use Illuminate\Http\Request;

class BusinessTaxiController extends Controller
{
    public function BusinessTexilistAll()
    {
        $taxis = BusinessTaxi::all();

        $data = $taxis->map(function ($taxi) {
            return [
                'id' => $taxi->id,
                'name' => $taxi->name,
                'class' => $taxi->class,
                'image' => !empty($taxi->image) ? asset('storage/' . ltrim($taxi->image, '/')) : null,
                'resource' => $taxi->resource,
                'income_per_hour' => $taxi->income_per_hour,
                'price' => $taxi->price,
                'created_at' => $taxi->created_at ? $taxi->created_at->toDateTimeString() : null,
                'updated_at' => $taxi->updated_at ? $taxi->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
