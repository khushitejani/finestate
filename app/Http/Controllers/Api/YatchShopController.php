<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YatchShop;
use Illuminate\Http\Request;

class YatchShopController extends Controller
{
    public function YatchShopListAll()
    {
        $yatchShops = YatchShop::all();

        $data = $yatchShops->map(function ($item) {
            $images = [];
            if (!empty($item->image)) {
                $decoded = json_decode($item->image, true);
                if (is_array($decoded)) {
                    $images = array_map(fn($img) => asset('storage/' . ltrim($img, '/')), $decoded);
                }
            }

            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
                 'images' => $images,
                'created_at' => $item->created_at ? $item->created_at->toDateTimeString() : null,
                'updated_at' => $item->updated_at ? $item->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
