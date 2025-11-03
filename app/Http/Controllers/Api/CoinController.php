<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coin;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    public function CoinListAll()
    {
        $coins = Coin::all();

        $data = $coins->map(function ($coin) {
            return [
                'id' => $coin->id,
                'name' => $coin->name,
                'price' => $coin->price,
                'years' => $coin->years,
                'image' => $coin->image ? asset('storage/' . ltrim($coin->image, '/')) : null,
                'created_at' => $coin->created_at ? $coin->created_at->toDateTimeString() : null,
                'updated_at' => $coin->updated_at ? $coin->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
