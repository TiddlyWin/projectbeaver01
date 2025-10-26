<?php

namespace App\Services\EveOnline;


use App\DTO\EveOnline\EveTokenDTO;
use App\Services\EveOnline\AuthHelpers\JwksMetadataService;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use InvalidArgumentException;
use JsonException;
use RuntimeException;

class EveTokenValidator
{
    public function __construct(
        protected JwksMetadataService $jwksMetadataService
    ) {}

    /**
     * @throws JsonException|InvalidArgumentException|RuntimeException
     */
    public function validate(string $jwt): EveTokenDTO
    {
        // Decode header to get alg/kid fast (don’t trust it yet)
        [$headerB64, $payloadB64, $sigB64] = $this->split($jwt);

        $rawHeader  = $this->jsonDecode($this->b64urlDecode($headerB64));
        $alg        = $rawHeader['alg'] ?? null;

        $cfg = config('services.eveonline');
        if (!is_string($alg) || $alg !== ($cfg['alg'] ?? 'RS256')) {
            throw new RuntimeException('Unexpected JWT alg: ' . ($alg ?: 'none'));
        }

        // Fetch JWKS and let firebase/php-jwt pick the right key by kid
        $jwks  = $this->jwksMetadataService->fetch();
        $keys  = JWK::parseKeySet($jwks);
        $decoded = (array) JWT::decode($jwt, $keys);

        // Claims checks
        $this->assertIssuer($decoded, (array) ($cfg['accepted_issuers'] ?? []));
        $this->assertNotExpired($decoded);
        $this->assertAudience($decoded, (string) ($cfg['accepted_audience'] ?? 'EVE Online'));

        return EveTokenDTO::fromPayload($decoded);
    }

    protected function split(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new RuntimeException('Invalid JWT format');
        }
        return $parts;
    }

    protected function b64urlDecode(string $s): string
    {
        $padded = strtr($s, '-_', '+/');
        $padded .= str_repeat('=', (4 - strlen($padded) % 4) % 4);
        $bin = base64_decode($padded, true);
        if ($bin === false) {
            throw new RuntimeException('Invalid base64url segment');
        }
        return $bin;
    }

    /**
     * @throws JsonException
     */
    protected function jsonDecode(string $json): array
    {
        /** @var array|null $arr */
        $arr = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        return $arr;
    }

    protected function assertIssuer(array $payload, array $acceptedIssuers): void
    {
        $iss = $payload['iss'] ?? null;
        if (!is_string($iss) || !in_array($iss, $acceptedIssuers, true)) {
            throw new RuntimeException('Invalid token issuer');
        }
    }

    protected function assertNotExpired(array $payload): void
    {
        $exp = $payload['exp'] ?? null;
        if (!is_int($exp) || $exp < time()) {
            throw new RuntimeException('Token has expired');
        }
    }

    protected function assertAudience(array $payload, string $expectedAud): void
    {
        if (!isset($payload['aud'])) {
            return;
        }

        $aud = $payload['aud'];
        $ok = is_string($aud) ? $aud === $expectedAud : (is_array($aud) && in_array($expectedAud, $aud, true));

        if (!$ok) {
            throw new RuntimeException('Invalid token audience');
        }
    }
}
