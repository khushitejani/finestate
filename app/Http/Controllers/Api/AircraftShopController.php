<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AircraftShop;
use Illuminate\Http\Request;

class AircraftShopController extends Controller
{
    public function AircraftShopListAll()
    {
        $shops = AircraftShop::all();

        $data = $shops->map(function ($shop) {
            return [
                'id' => $shop->id,
                'name' => $shop->name,
                'price' => $shop->price,
                'description' => $shop->description,
                'image' => $shop->image ? asset('storage/' . ltrim($shop->image, '/')) : null,
                'created_at' => $shop->created_at ? $shop->created_at->toDateTimeString() : null,
                'updated_at' => $shop->updated_at ? $shop->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
