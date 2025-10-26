<?php


namespace App\Services\EveOnline\AuthHelpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class JwksMetadataService
{
    public function __construct(
        protected string $metadataUrl,
        protected int    $cacheTime = 300
    )
    {
    }

    public function fetch(): array
    {
        return Cache::remember('jwks_metadata', $this->cacheTime, function () {
            $metadataResponse = Http::get($this->metadataUrl);
            if ($metadataResponse->failed()) {
                throw new RuntimeException('Failed to fetch EVE SSO metadata: ' . $metadataResponse->body());
            }

            $metadata = $metadataResponse->json();
            if (!isset($metadata['jwks_uri'])) {
                throw new RuntimeException('Invalid EVE SSO metadata: missing jwks_uri');
            }

            $jwksResponse = Http::get($metadata['jwks_uri']);
            if ($jwksResponse->failed()) {
                throw new RuntimeException('Failed to fetch JWKS: ' . $jwksResponse->body());
            }

            $jwks = $jwksResponse->json();
            if (!isset($jwks['keys']) || !is_array($jwks['keys'])) {
                throw new RuntimeException('Invalid JWKS response: missing keys');
            }

            return $jwks;
        });
    }
}
