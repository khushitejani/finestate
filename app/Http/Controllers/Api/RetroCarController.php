<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RetroCar;
use Illuminate\Http\Request;

class RetroCarController extends Controller
{
    public function RetroCarListAll()
    {
        $retroCars = RetroCar::all();

        $data = $retroCars->map(function ($car) {
            return [
                'id' => $car->id,
                'name' => $car->name,
                'years' => $car->years,
                'image' => $car->image ? asset('storage/' . ltrim($car->image, '/')) : null,    
                'price' => $car->price,
                'created_at' => $car->created_at?->toDateTimeString(),
                'updated_at' => $car->updated_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
