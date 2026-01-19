<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'to',
        'message',
        'status', 
        'response',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
