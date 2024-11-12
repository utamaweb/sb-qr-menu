<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use App\Models\User;
use App\Models\Warehouse;
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

        // Process to check if user warehouse is not self service
        $user = User::where('email', $request->email)->first();
        $warehouse = Warehouse::where('id', $user->warehouse_id)->first();

        if(($user->hasRole('Customer')) AND ($warehouse->is_self_service == 0)) {
            return response()->json([
                'status' => false,
                'message' => 'User warehouse is not self service'
            ], 200);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            $token = JWTAuth::attempt($credentials);

            if (!$token) {
                return response()->json(['message' => 'Login credentials are invalid'], 400);
            }

            $user = getUser($request->email);
            // $userResponse->token = $token;
            // $user->token_expires_in = auth()->factory()->getTTL() * 60;
            // $userResponse->token_expires_in = auth('api')->factory()->getTTL() * 60;
            // $userResponse->token_type = 'bearer';

            $refreshToken = JWTAuth::fromUser($user);

            // Menyertakan token refresh bersama dengan token akses dalam respons
            $response = [
                'user' => $user,
                'access_token' => $token,
                'refresh_token' => $refreshToken
            ];


            return response()->json($response);

        } catch (JWTException $e) {
            \Log::emergency("File:" . $th->getFile() . " Line:" . $th->getLine() . " Message:" . $th->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Log out success']);
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        try {
            $token = JWTAuth::parseToken()->refresh();

            // Mendapatkan user terkait dengan token baru
            $user = JWTAuth::setToken($token)->toUser();
            $refreshToken = JWTAuth::fromUser($user);

            // Menyertakan token akses baru dalam respons
            $response = [
                'user' => $user,
                'access_token' => $token,
                'refresh_token' => $refreshToken
            ];

            return response()->json($response);

        } catch (JWTException $e) {
            \Log::emergency("File:" . $th->getFile() . " Line:" . $th->getLine() . " Message:" . $th->getMessage());
            return response()->json(['message' => 'Token refresh failed'], 500);
        }
    }

}
