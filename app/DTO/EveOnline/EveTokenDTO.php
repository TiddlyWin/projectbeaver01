<?php


namespace App\DTO\EveOnline;

use Carbon\CarbonImmutable;

final readonly class EveTokenDTO
{
    public function __construct(
        public int     $characterId,
        public ?string $owner,
        public string  $issuer,
        public string  $subject,
        public int     $expiry,
        public ?string $audience = null,
        public ?string $name = null,
        /** @var string[]|null */
        public ?array  $scopes = null,
        /** raw decoded payload for advanced callers */
        public array   $payload = [],
    )
    {
    }

    public function isExpired(): bool
    {
        return CarbonImmutable::now()->timestamp > $this->expiry;
    }

    public static function fromPayload(array $payload): self
    {
        if (!isset($payload['sub'], $payload['iss'], $payload['exp'])) {
            throw new \InvalidArgumentException('Missing required JWT fields (sub, iss, exp).');
        }

        // EVE format: "CHARACTER:EVE:123456"
        $characterId = (int)str_replace('CHARACTER:EVE:', '', (string)$payload['sub']);

        $audience = null;
        if (isset($payload['aud'])) {
            $audience = is_array($payload['aud']) ? $payload['aud'][0] : (string)$payload['aud'];
        }


        return new self(
            characterId: $characterId,
            owner: $payload['owner'] ?? null,
            issuer: (string)$payload['iss'],
            subject: (string)$payload['sub'],
            expiry: (int)$payload['exp'],
            audience: $audience,
            name: $payload['name'] ?? null,
            scopes: isset($payload['scp']) ? (array)$payload['scp'] : null,
            payload: $payload,
        );
    }
}
