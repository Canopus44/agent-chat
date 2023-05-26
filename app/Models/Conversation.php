<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Conversation extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        '_id',
        'session_chat',
        'recipient_id',
        'sender_id',
        'agent',
        'status',
        'created_at',
        'object',
        'last_message'
    ];
}
