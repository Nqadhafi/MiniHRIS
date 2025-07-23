<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\WhatsappSetting;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    protected $client;
    protected $apiKey;
    protected $delay;
    protected $baseUrl;
    protected $provider;

    /**
     * WhatsAppService constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->client = new Client();

        $setting = WhatsappSetting::where('is_active', true)->first();

        if (!$setting) {
            Log::error('Tidak ada pengaturan WhatsApp aktif.');
            throw new Exception("Belum ada pengaturan WhatsApp aktif.");
        }

        $this->apiKey = $setting->api_key;
        $this->delay = $setting->delay_between_messages ?: 1;
        $this->provider = $setting->service_provider;

        switch ($this->provider) {
            case 'fontee':
                $this->baseUrl = 'https://api.fontee.id/v1/message/send';
                break;
            case 'zaviago':
                $this->baseUrl = 'https://api.zaviago.com/v1/send_message';
                break;
            case 'restqa':
                $this->baseUrl = 'https://api.restqa.io/v1/whatsapp/send';
                break;
            default:
                throw new Exception("Provider WhatsApp tidak dikenali: {$this->provider}");
        }
    }

    /**
     * Kirim pesan WhatsApp ke nomor tertentu.
     *
     * @param string $phoneNumber
     * @param string $message
     * @return bool
     */
    public function sendMessage($phoneNumber, $message)
    {
        try {
            $response = $this->client->post($this->baseUrl, [
                'headers' => $this->buildHeaders(),
                'json' => $this->buildPayload($phoneNumber, $message),
                'timeout' => 10,
            ]);

            if ($response->getStatusCode() === 200) {
                Log::info("Pesan berhasil dikirim ke $phoneNumber");
                sleep($this->delay);
                return true;
            }

            Log::warning("Gagal mengirim pesan ke $phoneNumber. Status: " . $response->getStatusCode());
            return false;
        } catch (Exception $e) {
            Log::error("Gagal mengirim pesan ke $phoneNumber. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Header HTTP sesuai provider.
     *
     * @return array
     */
    protected function buildHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
    }

    /**
     * Payload JSON sesuai provider.
     *
     * @param string $phone
     * @param string $message
     * @return array
     */
    protected function buildPayload($phone, $message)
    {
        switch ($this->provider) {
            case 'fontee':
                return [
                    'receiver' => $phone,
                    'message' => $message,
                ];
            case 'zaviago':
                return [
                    'phone' => $phone,
                    'message' => $message,
                ];
            case 'restqa':
                return [
                    'to' => $phone,
                    'message' => $message,
                ];
            default:
                return [];
        }
    }
}
