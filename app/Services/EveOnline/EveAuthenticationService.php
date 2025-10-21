<?php

namespace App\Services\EveOnline;

use App\Models\Character;
use App\Models\User;
use App\Repositories\EVECharacterRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use JsonException;
use RuntimeException;
use Throwable;

class EveAuthenticationService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected EveApiService          $eveApiService,
        protected EveTokenValidator      $eveTokenValidator,
        protected EVECharacterRepository $eveCharacterRepository
    )
    {
        //
    }

    /**
     * @throws JsonException
     * @throws Throwable
     */
    public function authenticate(SocialiteUser $eveIdentity): JsonResponse
    {
        Log::info('Authenticating user from EVE SSO');

        $validated = $this->eveTokenValidator->validate($eveIdentity->token);
        $tokenObj = [
            'access_token' => $eveIdentity->token,
            'refresh_token' => $eveIdentity->refreshToken,
            'expires_in' => $eveIdentity->expiresIn,
            'scopes' => $eveIdentity->approved_scopes
        ];

        Log::info('Validated Token: Token valid');

        // Check the character owner hash matches the one in the token
        // TODO:: Sense check this and perhaps add more checking against the token?
        if ($eveIdentity->attributes['character_owner_hash'] !== $validated['owner']) {
            Log::warning('Character owner hash does not match');
            throw new RuntimeException('Character owner hash does not match');
        }

        //Use the validated token to supply the necessary data
        $characterId = $validated['character_id'];
        $eveCharacterName = $validated['name'];

        $user = Auth::user();
        $character = Character::where('eve_character_id', $characterId)->first();

        Log::info('User: ' . $user);

        // TODO:: Still a work in progress
        if (!$user) {
           if(!$character) {
               Log::info('Creating new character for user');
               $user = User::firstOrCreate(['email' => "eve_{$characterId}@local"], [
                   'name' => $eveCharacterName ?: 'EVE Pilot',
                   'password' => bcrypt(str()->random(32)),
               ]);

               $this->eveCharacterRepository->createOrUpdateCharacter($user, (int)$characterId, $validated, $tokenObj);
           }

            $user = $character->user;
           if($character->id !== $user->main_character_id) {
               throw new RuntimeException('Please log in with your main character');
           }
        }

        // Log the user in
        Auth::login($user);

        Log::info('User authenticated and logged in', [
            'user_id' => $user->id,
            'character_id' => $character->eve_character_id,
        ]);

        return response()->json([
            'user_id' => $user->id,
            'character_id' => $character->eve_character_id,
            'needs_email' => preg_match('/^eve_\d+@local$/', $user->email ?? '') === 1,
        ]);
    }
}
