<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stamp;
use Illuminate\Http\Request;

class StampController extends Controller
{
    public function StampListAll()
    {
        $stamps = Stamp::all();

        $data = $stamps->map(function ($stamp) {
            return [
                'id' => $stamp->id,
                'name' => $stamp->name,
                'years' => $stamp->years,
                'image' => $stamp->image ? asset('storage/' . ltrim($stamp->image, '/')) : null,
                'price' => $stamp->price,
                'created_at' => $stamp->created_at ? $stamp->created_at->toDateTimeString() : null,
                'updated_at' => $stamp->updated_at ? $stamp->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
