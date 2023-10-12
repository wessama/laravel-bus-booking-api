<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;

#[Group("Authentication", "APIs for authenticating users")]
class AuthController extends Controller
{
    /**
     * Login
     *
     * This endpoint allows you to obtain a token for an existing user.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('token');

            return response()->json([
                'token' => $token->plainTextToken,
                'expires_at' => $token->accessToken->expires_at
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'error' => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Logout
     *
     * This endpoint allows you to invalidate a token.
     */
    #[Authenticated]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
