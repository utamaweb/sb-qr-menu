<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            $token = JWTAuth::attempt($credentials);

            if (!$token) {
                return response()->json(['message' => 'Login credentials are invalid'], 400);
            }

            $userResponse = getUser($request->email);
            // $userResponse->token = $token;
            // $userResponse->token_expires_in = auth()->factory()->getTTL() * 60;
            // $userResponse->token_expires_in = auth('api')->factory()->getTTL() * 60;
            // $userResponse->token_type = 'bearer';

            $response['user'] = $userResponse;
            $response['token'] = $token;


            return response()->json($response);

        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Log out success']);
    }

    public function refreshToken()
    {
        try {
            $token = JWTAuth::parseToken()->refresh();
            return response()->json(['token' => $token], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to refresh token'], 401);
        }
    }

}
