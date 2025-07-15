<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Auth;
use Cache;
use DB;

class AuthController extends Controller
{
    public function index()
    {
        $general_setting =  DB::table('general_settings')->latest()->first();
        if(auth()->user()){
            return redirect()->route('admin.dashboard');
        }

        if(!$general_setting) {
            \DB::unprepared(file_get_contents('public/tenant_necessary.sql'));
            $general_setting =  Cache::remember('general_setting', 60*60*24*365, function () {
                return DB::table('general_settings')->latest()->first();
            });
        }
        $numberOfUserAccount = \App\Models\User::where('is_active', true)->count();

        $siteKey = env('CLOUDFLARE_TURNSTILE_SITE_KEY'); // Your Cloudflare site key
        $appEnv = env('APP_ENV', 'production');

        return view('backend.auth.login', compact('general_setting', 'numberOfUserAccount', 'siteKey', 'appEnv'));
    }

    public function login(Request $request)
    {
        // Only validate CAPTCHA in production environment
        $appEnv = env('APP_ENV', 'production');

        if ($appEnv === 'production') {
            // Validate Cloudflare Turnstile CAPTCHA
            $cfResponse = $request->input('cf-turnstile-response');
            if (!$cfResponse) {
                return redirect()->back()->with('captcha_error', 'Please complete the CAPTCHA challenge.');
            }

            // Verify CAPTCHA with Cloudflare
            $secretKey = env('CLOUDFLARE_TURNSTILE_SECRET_KEY');
            $ip = $request->ip();
            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $cfResponse,
                'remoteip' => $ip,
            ]);

            $result = $response->json();
            if (!$result['success']) {
                return redirect()->back()->with('captcha_error', 'CAPTCHA validation failed. Please try again.');
            }
        }
        // In local/development environment, bypass CAPTCHA validation

        $credential = $request->only('username', 'password');

        if (Auth::guard('web')->attempt($credential)) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()
                ->with('error', 'Invalid Credential');
    }

    public function logout()
    {
        Auth::guard('web')
            ->logout();

        return redirect()
            ->route('admin.auth.login');
    }
}
