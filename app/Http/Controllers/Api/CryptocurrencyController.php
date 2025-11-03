<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cryptocurrency;
use Illuminate\Http\Request;

class CryptocurrencyController extends Controller
{
    public function CryptoCurrancyListAll()
    {
        $cryptos = Cryptocurrency::all();

        $data = $cryptos->map(function ($crypto) {
            $chartData = null;
            if (!empty($crypto->day_prices)) {
                $chartData = getChartDataForToday($crypto);
            }

            return [
                'id' => $crypto->id,
                'name' => $crypto->name,
                'symbol' => $crypto->symbol,
                'image' => !empty($crypto->image) ? asset('storage/' . ltrim($crypto->image, '/')) : null,
                'price' => $crypto->price,
                'cryptocurrencies_cap' => $crypto->cryptocurrencies_cap,
                'available_for_purchase' => $crypto->available_for_purchase,
                'chart' => $chartData,
                'created_at' => $crypto->created_at ? $crypto->created_at->toDateTimeString() : null,
                'updated_at' => $crypto->updated_at ? $crypto->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
