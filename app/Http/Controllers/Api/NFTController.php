<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NFT;
use Illuminate\Http\Request;

class NFTController extends Controller
{
  public function NFTListAll()
    {
        $nfts = NFT::all();

        $data = $nfts->map(function ($nft) {
            return [
                'id' => $nft->id,
                'name' => $nft->name,
                'image' => $nft->image ? asset('storage/' . ltrim($nft->image, '/')) : null,
                'price' => $nft->price,
                'description' => $nft->description,
                'created_at' => $nft->created_at ? $nft->created_at->toDateTimeString() : null,
                'updated_at' => $nft->updated_at ? $nft->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
