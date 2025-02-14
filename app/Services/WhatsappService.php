<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

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

    /**
     * Return configs
     */
    public function getConfig() {
        return collect([
            'url' => $this->url,
            'session' => $this->session
        ]);
    }

    /**
     * Get sessions list
     */
    public function getSessions() {
        $client = new Client();
        $url = $this->url . '/session';

        $response = $client->get($url);
        $data = json_decode($response->getBody(), true);

        return response()->json($data);
    }

    /**
     * Get session details
     */
    public function getSessionDetail() {
        $client = new Client();
        $url = $this->url . '/session/details';

        try {
            $response = $client->get($url, [
                'query' => [
                    'session' => $this->session
                ]
            ]);
            $data = json_decode($response->getBody(), true);

            $message = NULL;

            if(empty($data)) {
                $message = 'Akun Whatsapp belum terhubung!';
            }

            return response()->json([
                'error' => false,
                'data' => $data,
                'message' => $message
            ]);
        } catch (ClientException $e) {
            $responseBody = json_decode($e->getResponse()->getBody(), true);
            return response()->json([
                'error' => true,
                'message' => $responseBody['message']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal menyambungkan layanan whatsapp!'
            ]);
        }
    }

    /**
     * Create session
     */
    public function createSession() {
        $client = new Client();
        $url = $this->url . '/session/start';

        try {
            $response = $client->post($url, [
                'json' => [
                    'session' => $this->session
                ]
            ]);
            $data = json_decode($response->getBody(), true);

            return response()->json([
                'error' => false,
                'data' => $data
            ]);
        } catch (ClientException $e) {
            $responseBody = json_decode($e->getResponse()->getBody(), true);
            return response()->json([
                'error' => true,
                'message' => $responseBody['message']
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal menyambungkan layanan whatsapp!'
            ]);
        }
    }

    /**
     * Logout session
     */
    public function logout() {
        $client = new Client();
        $url = $this->url . '/session/logout';

        try {
            $response = $client->get($url, [
                'query' => [
                    'session' => $this->session
                ]
            ]);
            $data = json_decode($response->getBody(), true);

            return response()->json([
                'error' => false,
                'data' => $data
            ]);
        } catch (ClientException $e) {
            $responseBody = json_decode($e->getResponse()->getBody(), true);
            return response()->json([
                'error' => true,
                'message' => $responseBody['message']
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal menyambungkan layanan whatsapp!'
            ]);
        }
    }

    /**
     * Check API connection
     */
    public function checkConnection() {
        $client = new Client();
        $url = $this->url . '/session/check-connection';

        try {
            $response = $client->get($url, [
                'query' => [
                    'session' => $this->session
                ]
            ]);
            $data = json_decode($response->getBody(), true);

            return response()->json([
                'error' => false,
                'data' => $data['data']
            ]);
        } catch (ClientException $e) {
            $responseBody = json_decode($e->getResponse()->getBody(), true);
            return response()->json([
                'error' => true,
                'message' => $responseBody['message']
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal menyambungkan layanan whatsapp!'
            ]);
        }
    }

    /**
     * Check number
     */
    public function checkNumber($number) {
        $client = new Client();
        $url = $this->url . '/message/check-number';

        try {
            $response = $client->get($url, [
                'query' => [
                    'session' => $this->session,
                    'number' => $number
                ]
            ]);
            $data = json_decode($response->getBody(), true);

            return response()->json([
                'error' => false,
                'data' => $data['data']
            ]);
        } catch (ClientException $e) {
            $responseBody = json_decode($e->getResponse()->getBody(), true);
            return response()->json([
                'error' => true,
                'message' => $responseBody['message']
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal menyambungkan layanan whatsapp!'
            ]);
        }
    }

    /**
     * Send message
     */
    public function sendMessage($number, $message) {
        $client = new Client();
        $url = $this->url . '/message/send-text';

        try {
            $response = $client->post($url, [
                'json' => [
                    'session' => $this->session,
                    'to' => $number,
                    'text' => $message
                ]
            ]);

            return true;
        } catch (ClientException $e) {
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
?>
