<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function CardListAll()
    {
        $cards = Card::all();

        $data = $cards->map(function ($card) {
            return [
                'id' => $card->id,
                'name' => $card->name,
                'image' => !empty($card->image) ? asset('storage/' . ltrim($card->image, '/')) : null,
                'price' => $card->price,
                'sign_price' => $card->sign_price,
                'created_at' => $card->created_at ? $card->created_at->toDateTimeString() : null,
                'updated_at' => $card->updated_at ? $card->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
