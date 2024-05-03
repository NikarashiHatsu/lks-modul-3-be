<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        try {
            $user->tokens()->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Logout failed: ' . $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Logout success',
        ]);
    }
}
