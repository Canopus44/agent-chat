<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Message extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'object',
        'idPage',
        'time',
        'messaging',
        'session_chat',
    ];
}
