<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Character;
use App\Models\CharacterToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Throwable;

class EVECharacterRepository
{
    /**
     * @param int $characterId
     * @return Character|null
     */
    public function findByEveCharacterId(int $characterId): ?Character
    {
        return Character::where('eve_character_id', $characterId)->with('user')->first();
    }

    /**
     * @param User $user
     * @param int $characterId
     * @param array $data
     * @param array $tokenObj
     * @return Character
     * @throws Throwable
     */
    public function createOrUpdateCharacter(User $user, int $characterId, array $data, array $tokenObj): Character
    {
        return DB::transaction(static function () use ($user, $characterId, $data, $tokenObj) {
            $character = Character::updateOrCreate(
                ['eve_character_id' => $characterId],
                [
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'portrait_url' => 'https://images.evetech.net/characters/'. $characterId .'/portrait?size=256' ?? null,
                    'metadata' => $data['metadata'] ?? [],
                ]
            );

            CharacterToken::updateOrCreate(
                ['character_id' => $character->id],
                [
                    'access_token' => Crypt::encryptString($tokenObj['access_token']),
                    'refresh_token' => Crypt::encryptString($tokenObj['refresh_token']),
                    'scopes' => $tokenObj['scopes'],
                    'expires_at' => $tokenObj['expires_in'],
                ]
            );

            // Set as the main character if the user has none
            if (!$user->main_character_id) {
                $user->forceFill(['main_character_id' => $character->id])->save();
            }

            return $character;
        });
    }
}
