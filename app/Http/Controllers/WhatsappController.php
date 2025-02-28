<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsappService;

class WhatsappController extends Controller
{
    protected $whatsapp;

    /**
     * Class constructor.
     */
    public function __construct() {
        $this->whatsapp = new WhatsappService();
    }

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

    /**
     * Get all sessions
     */
    public function sessions() {
        return $this->whatsapp->getSessions();
    }

    /**
     * Get session details
     */
    public function sessionDetails() {
        return $this->whatsapp->getSessionDetail();
    }

    /**
     * Create session
     */
    public function createSession() {
        return $this->whatsapp->createSession();
    }

    /**
     * Logout session
     */
    public function logout() {
        return $this->whatsapp->logout();
    }

    /**
     * Check API connection
     */
    public function checkConnection() {
        return $this->whatsapp->checkConnection();
    }

    /**
     * Check number
     */
    public function checkNumber($number) {
        return $this->whatsapp->checkNumber($number);
    }

    /**
     * Send message
     */
    public function sendMessage($number, $message) {
        return $this->whatsapp->sendMessage($number, $message);
    }
}
