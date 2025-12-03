<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function Policylist()
    {
        $policies = Policy::all();

        return response()->json([
            'status' => 'success',
            'data' => $policies
        ]);
    }
}
