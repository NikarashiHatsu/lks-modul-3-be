<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Auth\RegisterRequest;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        $userData = $request->validated();

        try {
            $user = User::create($userData);

            $token = $user->createToken('facegram');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Register failed: ' . $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Register success',
            'token' => $token->plainTextToken,
            'user' => $user,
        ]);
    }
}
