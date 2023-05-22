<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'https://graph.facebook.com/*',
        'http://webhooks-meta.test/*',
        'https://ae7c-167-0-187-241.ngrok-free.app/*',
        'https://developers.facebook.com/*',
        'https://dist.test/*',
        'https://app.test/*',
        '/*'
    ];
}
