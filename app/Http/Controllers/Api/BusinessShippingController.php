<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessShipping;
use Illuminate\Http\Request;

class BusinessShippingController extends Controller
{
    public function BusinessShippingListAll()
    {
        $shippings = BusinessShipping::all();

        $data = $shippings->map(function ($ship) {
            return [
                'id' => $ship->id,
                'name' => $ship->name,
                'category' => $ship->category,
                'image' => !empty($ship->image)
                    ? asset('storage/' . ltrim($ship->image, '/'))
                    : null,
                'resource' => $ship->resource,
                'income_per_hour' => $ship->income_per_hour,
                'price' => $ship->price,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
