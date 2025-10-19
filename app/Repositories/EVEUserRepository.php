<?php

namespace App\Repositories;

use App\Models\Character;

class EVEUserRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findByEveCharacterId(int $eveCharacterId): Character
    {
        return Character::where('eve_character_id', $eveCharacterId)->first();
    }

    public function createOrUpdateFromEveLogin(int $eveCharacterId, array $payload): Character
    {
        dd($payload);
//        return Character::updateOrCreate(
//            ['eve_character_id' => $eveCharacterId],
//            [
//                'user_id' => $payload[],
//                'name' => $eveCharacterName,
//                'portrait_url' => $avatar,
//            ]
//        );
    }
}
