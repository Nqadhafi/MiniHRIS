<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $to, $message, $tries = 3, $backoff = 60; // retry setelah 60 detik

    public function __construct($to, $message)
    {
        $this->to = $to;
        $this->message = $message;
    }

    public function handle()
    {
        $wa = new WhatsAppService();
        $result = $wa->sendMessage($this->to, $this->message);

        if (!$result['success']) {
            Log::warning("Gagal kirim WA ke {$this->to}: " . $result['error']);
            throw new \Exception("Gagal kirim WA");
        }
    }
}
