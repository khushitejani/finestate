<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Share;
use Illuminate\Support\Facades\Storage;


class ShareController extends Controller
{
    public function ShareListAll()
    {
        $shares = Share::all();

        $data = $shares->map(function ($share) {
            // $chartData = getChartDataForToday($share);
            $today = now()->toDateString();
            $dayPrices = $share->day_prices ?? [];
            $chartData = $dayPrices[$today] ?? [];
            return [
                'id' => $share->id,
                'name' => $share->name,
                'image' => !empty($share->image) ? asset('storage/' . ltrim($share->image, '/')) : null,
                'share_price' => $share->share_price,
                'dividend' => $share->dividend,
                'time_period' => $share->time_period,
                'capitalization' => $share->capitalization,
                'available_shares' => $share->available_shares,
                'chart' => $chartData,
                'created_at' => $share->created_at ? $share->created_at->toDateTimeString() : null,
                'updated_at' => $share->updated_at ? $share->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
