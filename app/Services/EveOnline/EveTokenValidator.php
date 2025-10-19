<?php

namespace App\Services\EveOnline;

use Exception;
use JsonException;
use RuntimeException;

class EveTokenValidator
{

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function validate(string $token): array
    {
        $tokenParts = explode('.', $token);

        if (count($tokenParts) !== 3) {
            throw new RuntimeException('Invalid JWT format');
        }

        $payload = json_decode(base64_decode($tokenParts[1]), true, 512, JSON_THROW_ON_ERROR);

        if (!$payload) {
            throw new RuntimeException('Invalid token payload');
        }


        $this->validateExpiry($payload);
        $this->validateIssuer($payload);

        return [
            'character_id' => $this->extractCharacterId($payload),
            'payload' => $payload,
        ];
    }

    protected function validateExpiry(array $payload): void
    {
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new RuntimeException('Token has expired');
        }
    }

    protected function validateIssuer(array $payload): void
    {
        if (!isset($payload['iss']) && $payload['iss'] !== 'https://login.eveonline.com') {
            throw new RuntimeException('Invalid token issuer');
        }
    }

    protected function extractCharacterId(array $payload): int
    {
        if (!isset($payload['sub'])) {
            throw new RuntimeException('No character ID in token');
        }

        // EVE tokens have format "CHARACTER:EVE:123456"
        $sub = str_replace('CHARACTER:EVE:', '', $payload['sub']);

        return (int) $sub;
    }
}
