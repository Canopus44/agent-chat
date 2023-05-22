<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Auth\User as Authenticatable;

class Agent extends Authenticatable implements JWTSubject
{

    protected $connection = 'mongodb';
    // Rest omitted for brevity

    protected $fillable = [
        'name',
        'email',
        'password',
        'operation',
        'agentUser',
        'agentToken',
        'agentStatus',

    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
