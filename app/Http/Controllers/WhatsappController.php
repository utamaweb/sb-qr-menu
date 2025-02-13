<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use Illuminate\Support\Facades\DB;

class WhatsappController extends Controller
{
    /**
     * Whatsapp configuration index
     */
    public function index() {
        // Get all configs
        $configs = Config::all();

        return view('backend.config.whatsapp', compact('configs'));
    }

    /**
     * Store whatsapp configuration
     */
    public function store(Request $request) {
        try {
            DB::beginTransaction();
            foreach ($request->input('config') as $key => $value) {
                Config::updateOrCreate(['key' => $key], ['value' => $value]);
            }

            DB::commit();
            return redirect()->route('whatsapp.index')->with('message', 'Configuration saved successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('whatsapp.index')->with('message', 'Configuration could not be saved. Please try again.');
        }
    }
}
