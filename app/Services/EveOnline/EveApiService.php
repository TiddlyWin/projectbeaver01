<?php

namespace App\Services\EveOnline;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class EveApiService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function refresh(string $refreshToken): array
    {
        $response = Http::withBasicAuth(config('services.eve.client_id'), config('services.eve.client_secret'))
            ->asForm()
            ->post('https://login.eveonline.com/v2/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

        try {
            $response->throw();
        } catch (RequestException $e) {
            throw new RuntimeException('EVE token refresh failed: ' . $e->getMessage());
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            throw new RuntimeException('EVE token refresh returned invalid payload');
        }

        return [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $refreshToken,
            'expires_in' => $data['expires_in'] ?? null,
            'scopes' => $data['scope'] ?? null,
        ];
    }
}
