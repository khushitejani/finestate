<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessSlot;
use Illuminate\Http\Request;

class BusinessSlotController extends Controller
{
    //
    public function BusinessSlotAllList()
    {
        $slots = BusinessSlot::all();

        $data = $slots->map(function ($slot) {
            return [
                'id' => $slot->id,
                'no' => $slot->no,
                'expansion_time' => $slot->expansion_time,
                'price' => $slot->price,
                'created_at' => $slot->created_at ? $slot->created_at->toDateTimeString() : null,
                'updated_at' => $slot->updated_at ? $slot->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }
}
