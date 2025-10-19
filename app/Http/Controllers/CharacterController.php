<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CharacterController
{
    public function setMain(Request $request, Character $character): JsonResponse
    {
        $user = $request->user();

        abort_unless($character->user_id === $user->id, 403);

        $user->main_character_id = $character->id;
        $user->save();

        return response()->json($user->load(['characters', 'mainCharacter']));
    }
}


