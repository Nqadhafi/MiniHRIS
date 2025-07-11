<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasbon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal_pengajuan',
        'jumlah',
        'keperluan',
        'status',
        'approved_by',
        'approved_at'
    ];

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

    // Relasi ke User (yang approve)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
