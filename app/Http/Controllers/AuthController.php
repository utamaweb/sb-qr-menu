<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Cache;
use DB;

class AuthController extends Controller
{
    public function index()
    {
        return phpinfo();
        $general_setting =  Cache::remember('general_setting', 60*60*24*365, function () {
            return DB::table('general_settings')->latest()->first();
        });

        if(!$general_setting) {
            \DB::unprepared(file_get_contents('public/tenant_necessary.sql'));
            $general_setting =  Cache::remember('general_setting', 60*60*24*365, function () {
                return DB::table('general_settings')->latest()->first();
            });
        }
        $numberOfUserAccount = \App\Models\User::where('is_active', true)->count();
        return view('backend.auth.login', compact('general_setting', 'numberOfUserAccount'));
    }

    public function login(Request $request)
    {
        $credential = $request->only('name', 'password');

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
