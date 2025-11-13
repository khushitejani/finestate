<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carshowroom;
use Illuminate\Http\Request;

class CarshowroomController extends Controller
{
    public function CarshowroomListAll()
    {
        $carshowrooms = Carshowroom::all();

        $data = $carshowrooms->map(function ($car) {
             $images = $car->images ? array_map(function ($image) {
            return asset('storage/' . ltrim($image, '/'));
        }, json_decode($car->images)) : [];
            return [
                'id' => $car->id,
                'name' => $car->name,
                'price' => $car->price,
                'images' => $images,
                'created_at' => $car->created_at ? $car->created_at->toDateTimeString() : null,
                'updated_at' => $car->updated_at ? $car->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
