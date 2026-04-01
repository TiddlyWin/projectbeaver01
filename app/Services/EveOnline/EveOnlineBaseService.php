<?php

namespace App\Services\EveOnline;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

abstract class EveOnlineBaseService
{
    protected string $baseUrl = 'https://esi.evetech.net/latest';

    protected function client(string $accessToken): PendingRequest
    {
        return Http::withToken($accessToken)
            ->baseUrl($this->baseUrl);
    }
}
