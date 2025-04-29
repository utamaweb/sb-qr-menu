<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

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
                return response()->json(['message' => 'Login credentials are invalid'], 500);
            }

            // check isExpired in warehouse
            $dateNow = \Carbon\Carbon::now()->format('Y-m-d');
            if($dateNow >= $warehouse->expired_at && $warehouse->expired_at != null) {
                return response()->json([
                    'status' => false,
                    'message' => config('custom_message.EXPIRE_MESSAGE')
                ], 500);
            }

            $user = getUser($request->email);
            // $userResponse->token = $token;
            // $user->token_expires_in = auth()->factory()->getTTL() * 60;
            // $userResponse->token_expires_in = auth('api')->factory()->getTTL() * 60;
            // $userResponse->token_type = 'bearer';

            $refreshToken = JWTAuth::fromUser($user);

            Cache::put('refresh_token_' . $user->id, $refreshToken, 60 * 60 * 24 * 7);

            // Menyertakan token refresh bersama dengan token akses dalam respons
            $response = [
                'user' => $user,
                'access_token' => $token,
                'refresh_token' => $refreshToken
            ];


            return response()->json($response);

        } catch (JWTException $e) {
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function logout()
    {
        auth()->logout();
        Cache::forget('refresh_token_' . auth('api')->id());
        return response()->json(['message' => 'Log out success']);
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        try {
            $cachedRefreshToken = Cache::get('refresh_token_' . auth('api')->id());
            // return $cachedRefreshToken;
            if (!$cachedRefreshToken || $cachedRefreshToken!== $refreshToken) {
                return response()->json(['message' => 'Invalid refresh token'], 500);
            }

            $token = JWTAuth::parseToken()->refresh();

            // Mendapatkan user terkait dengan token baru
            $user = JWTAuth::setToken($token)->toUser();
            $refreshToken = JWTAuth::fromUser($user);

            Cache::forget('refresh_token_' . auth('api')->id());
            Cache::put('refresh_token_' . $user->id, $refreshToken, 60 * 60 * 24 * 7);

            // Menyertakan token akses baru dalam respons
            $response = [
                'user' => $user,
                'access_token' => $token,
                'refresh_token' => $refreshToken
            ];

            return response()->json($response);

        } catch (JWTException $e) {
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            return response()->json(['message' => 'Token refresh failed'], 500);
        }
    }

}
