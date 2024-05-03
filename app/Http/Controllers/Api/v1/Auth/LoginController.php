<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\v1\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        if (! Auth::attempt($request->validated())) {
            return response()->json([
                'message' => 'Wrong username or password',
            ], 401);
        }

        $user = User::query()
            ->where('username', $request->username)
            ->first();

        $token = $user->createToken('facegram');

        return response()->json([
            'message' => 'Login success',
            'token' => $token->plainTextToken,
            'user' => $user,
        ]);
    }
}
