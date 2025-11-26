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
             $images = [];

            if (!empty($island->images)) {
                if (is_string($island->images)) {
                    $imagesArray = json_decode($island->images, true);
                } elseif (is_array($island->images)) {
                    $imagesArray = $island->images;
                } else {
                    $imagesArray = [];
                }
                $images = array_map(function ($img) {
                    return asset('storage/' . ltrim(str_replace('\\/', '/', $img), '/'));
                }, $imagesArray);
            }
            if (empty($images)) {
                $images[] = asset('default.jpeg');
            }
            return [
                'id' => $island->id,
                'name' => $island->name,
                'image' => $images,
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
