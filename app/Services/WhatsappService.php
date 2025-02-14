<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected $url, $session;

    /**
     * Class constructor.
     */
    public function __construct() {
        $this->url = config('app_config.api_url');
        $this->session = config('app_config.session_name');
    }
}
?>
