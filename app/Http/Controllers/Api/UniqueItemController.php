<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UniqueItem;
use Illuminate\Http\Request;

class UniqueItemController extends Controller
{
    public function UniqueItemListAll()
    {
        $items = UniqueItem::all();

        $data = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'years' => $item->years,
                'image' => $item->image ? asset('storage/' . ltrim($item->image, '/')) : null,
                'price' => $item->price,
                'created_at' => $item->created_at?->toDateTimeString(),
                'updated_at' => $item->updated_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
