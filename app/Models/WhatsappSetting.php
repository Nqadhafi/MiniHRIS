<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_key',
        'sender_phone',
        'delay_between_messages',
        'is_active',
        'service_provider'
    ];
}