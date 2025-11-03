<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function PropertyListAll()
    {
        $properties = Property::all();

        $data = $properties->map(function ($property) {
            $images = [];

            if ($property->image) {
                $decoded = json_decode($property->image, true);
                if (is_array($decoded)) {
                    $images = array_map(fn($img) => asset('storage/' . ltrim($img, '/')), $decoded);
                }
            }

            return [
                'id' => $property->id,
                'name' => $property->name ?? null,
                'images' => $images,
                'price' => $property->price,
                'address' => $property->address,
                'income_per_hour' => $property->income_per_hour,
                'created_at' => $property->created_at ? $property->created_at->toDateTimeString() : null,
                'updated_at' => $property->updated_at ? $property->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
