<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Character;
use App\Models\CharacterToken;

class EVEUserRepository
{
    public function findByEveCharacterId(int $characterId): ?User
    {
        return Character::where('eve_character_id', $characterId)->first()?->user;
    }

    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
        ]);
    }

    public function createOrUpdateCharacter(User $user, int $characterId, array $data): Character
    {
        $character = Character::updateOrCreate(
            ['eve_character_id' => $characterId],
            [
                'user_id' => $user->id,
                'name' => $data['name'],
                'eve_character_id' => $characterId,
                'portrait_url' => $data['portrait_url'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ]
        );

        CharacterToken::updateOrCreate(
            ['character_id' => $character->id],
            [
                'access_token' => encrypt($data['token']),
                'refresh_token' => encrypt($data['refresh_token']),
                'expires_at' => $data['expires_at'],
            ]
        );

        // Set as main character if user has none
        if (!$user->main_character_id) {
            $user->update(['main_character_id' => $character->id]);
        }

        return $character;
    }
}
