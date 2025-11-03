<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\JsonResponse;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'current_password'          => ['required'],
            'new_password'              => ['required', 'string', 'min:6'],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ]);
        $user = auth('admin')->user();
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully.',
        ]);
    }
}
