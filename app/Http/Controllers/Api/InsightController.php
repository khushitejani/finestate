<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insight;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    public function InsightListAll()
    {
        $insights = Insight::all();

        $data = $insights->map(function ($insight) {
            return [
                'id' => $insight->id,
                'name' => $insight->name,
                'years' => $insight->years ?? null,
                'conditions' => $insight->conditions,
                'image' => $insight->image ? asset('storage/' . ltrim($insight->image, '/')) : null,
                'created_at' => $insight->created_at?->toDateTimeString(),
                'updated_at' => $insight->updated_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
