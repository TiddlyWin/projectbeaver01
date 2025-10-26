<?php

namespace App\Services\EveOnline;

use App\DTO\EveOnline\EveTokenDTO;
use App\Models\Character;
use App\Models\CharacterToken;
use App\Models\User;
use App\Repositories\EVECharacterRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use JsonException;
use RuntimeException;
use Throwable;

/**
 * Service for handling EVE Online authentication and character linkage.
 */
readonly class EveCharacterAuthenticationService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private EveTokenValidator      $eveTokenValidator,
        private EVECharacterRepository $eveCharacterRepository
    ){
    }

    /**
     * Authenticate a user via EVE SSO.
     *
     * This method handles the authentication process using an EVE SSO response. It validates the token,
     * ensures the integrity of the owner hash, extracts necessary information about the character,
     * and proceeds to either link the character to an existing user or log the user in using the character details.
     *
     * @param SocialiteUser $eveIdentity The user identity provided by the EVE SSO service.
     * @return JsonResponse The response indicating the result of authentication.
     * @throws RuntimeException|JsonException If the token payload is invalid or missing necessary fields.
     * @throws Throwable
     */
    public function authenticate(SocialiteUser $eveIdentity): JsonResponse
    {
        Log::info('[EveAuth] Authenticating user from EVE SSO');

        $validatedDTO = $this->eveTokenValidator->validate($eveIdentity->token);
        $tokenObj = $this->makeTokenObject($eveIdentity);

        Log::info('[EveAuth] Validated Token: Token valid');

        $this->assertOwnerHash($eveIdentity, $validatedDTO->payload);

        Log::info('[EveAuth] Owner hash verified');

        //Use the validated token to supply the necessary data
        $characterId = $validatedDTO->characterId ?? 0;
        $eveCharacterName = $validatedDTO->name ?? '';

        if ($characterId <= 0) {

            Log::error('[EveAuth] Missing character ID or name in token payload', [
                'character_id' => $characterId,
                'name' => $eveCharacterName,
            ]);

            throw new RuntimeException('[EveAuth] Invalid token payload: missing character ID or name');

        }

        // Slightly more readable to separate the two paths
        if(Auth::check()) {
            Log::info('[EveAuth] User already authenticated, linking character to existing user.');
            return $this->linkCharacter(Auth::user(), $characterId, $validatedDTO, $tokenObj);
        }

        return $this->loginViaCharacter($characterId, $eveCharacterName, $validatedDTO, $tokenObj);

    }

    /**
     * @param SocialiteUser $eveIdentity
     * @return array
     */
    private function makeTokenObject(SocialiteUser $eveIdentity): array
    {
        return [
            'access_token' => $eveIdentity->token,
            'refresh_token' => $eveIdentity->refreshToken,
            'expires_in' => $eveIdentity->expiresIn,
            'scopes' => $eveIdentity->scopes
        ];
    }

    /**
     * Assert that the owner hash of the given EVE identity matches the validated data.
     *
     * @param SocialiteUser $eveIdentity The EVE identity obtained via social authentication.
     * @param array $payload
     */
    private function assertOwnerHash(SocialiteUser $eveIdentity, array $payload): void
    {
        $expected = $payload['owner'] ?? null;
        $actual = $eveIdentity->attributes['character_owner_hash'] ?? null;

        if ($expected !== $actual) {
            Log::warning('[EveAuth] Character owner hash mismatch', [
                'expected' => $expected,
                'actual' => $actual,
            ]);

            throw ValidationException::withMessages([
                'owner_hash' => 'Character owner hash does not match expected value.',
            ]);
        }
    }

    /**
     * Handle login via an EVE character.
     *
     * This method attempts to authenticate a user based on the provided character ID.
     * If the character does not exist in the repository, it creates a new character and associated user.
     * If the character exists, it resolves the corresponding user.
     * The authenticated user is then logged in, and a JSON response is returned.
     *
     * @param int $characterId The EVE character ID used for authentication.
     * @param string $name The name of the character (can be empty).
     * @param EveTokenDTO $validated Validated data for the character.
     * @param array $tokenData Token data associated with the character.
     * @return JsonResponse         JSON response indicating authentication status or additional actions.
     * @throws Throwable
     */
    private function loginViaCharacter(int $characterId, string $name, EveTokenDTO $validated, array $tokenData): JsonResponse
    {
        $character = $this->eveCharacterRepository->findByEveCharacterId($characterId);

        if (!$character) {
            Log::info('[EveAuth] Creating new character and user');

            $user = User::firstOrCreate(
                ['email' => "eve_{$characterId}@local"],
                [
                    'name' => $name ?: 'EVE Pilot',
                    'password' => Hash::make(Str::random(32)),
                    'owner_hash' => $validated->owner ?? null
                ]

            );

            $this->eveCharacterRepository->createOrUpdateCharacter($user, $characterId, $validated, $tokenData);

        } else {

            Log::info('[EveAuth] Found existing character, resolving user.');
            try {

                $user = $this->resolveUserFromCharacter($character);

            } catch (ValidationException $e) {
                Log::warning('[EveAuth] User resolution failed', [
                    'character_id' => $characterId,
                    'errors' => $e->errors(),
                ]);
                throw $e;
            }
        }

        Auth::login($user);

        Log::info('[EveAuth] User authenticated', [
            'user_id' => $user->id,
            'character_id' => $characterId
        ]);

        return $this->needsEmailResponse($user);
    }

    /**
     * Link a character to an existing user.
     *
     * @param User $user The user to link the character to.
     * @param int $characterId The ID of the character to be linked.
     * @param EveTokenDTO $validated The validated data for the character.
     * @param array $tokenData The token data for authentication.
     *
     * @return JsonResponse
     * @throws Throwable
     */
    private function linkCharacter(User $user, int $characterId, EveTokenDTO $validated, array $tokenData): JsonResponse
    {
        Log::info('[EveAuth] Linking character to existing user');

        if($this->eveCharacterRepository->existForOtherUser($characterId, $user->id)) {
            Log::warning('[EveAuth] Character already linked to another user', [
                'user_id' => $user->id,
                'character_id' => $characterId,
            ]);

            throw ValidationException::withMessages([
                'character' => 'This character is already linked to another user account.',
            ]);
        }

        $this->eveCharacterRepository->createOrUpdateCharacter($user, $characterId, $validated, $tokenData);

        Log::info('[EveAuth] Linked character to existing user', [
            'user_id' => $user->id,
            'character_id' => $characterId,
        ]);

        return $this->needsEmailResponse($user);
    }

    /**
     * Resolves the user associated with the given character.
     *
     * @param Character $character The character to resolve the user from.
     * @return User The associated user.
     * @throws ValidationException If the character is not linked to any user or if the character
     *                             is not set as the user's main character.
     */
    private function resolveUserFromCharacter(Character $character): User
    {
        $user = $character->user ?? null;

        if (!$user) {
            throw ValidationException::withMessages([
                'character' => 'The character you are trying to log in with is not linked to any user account.',
            ]);
        }

        if ($character->id !== $user->main_character_id) {
            throw ValidationException::withMessages([
                'main_character' => 'The character you are trying to log in with is not set as your main character.',
            ]);
        }
        Log::info('[EveAuth] Resolving user from character');
        return $user;
    }

    /**
     * Determine if the user needs an email response based on their email address pattern.
     *
     * @param User $user The user instance to check.
     * @return JsonResponse The JSON response indicating whether the user needs an email response.
     */
    private function needsEmailResponse(User $user): JsonResponse
    {
        return response()->json([
            'needs_email' => preg_match('/^eve_\d+@local$/', $user->email ?? '') === 1,
        ]);
    }

}
