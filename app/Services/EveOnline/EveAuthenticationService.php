<?php

namespace App\Services\EveOnline;

use App\Models\Character;
use App\Models\User;
use App\Repositories\EVEUserRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use JsonException;

class EveAuthenticationService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected EveApiService $eveApiService,
        protected EveTokenValidator $eveTokenValidator,
        protected EVEUserRepository $eveUserRepository
    )
    {
        //
    }

    /**
     * @throws JsonException
     */
    public function authenticate(SocialiteUser $eveIdentity): RedirectResponse|Redirector
    {
        $validated = $this->eveTokenValidator->validate($eveIdentity->token);
        $characterId = $validated['character_id'];

        $user = Auth::user();
        $email = $eveIdentity->email ?? $eveIdentity->getEmail();
        $eveCharacterName = $eveIdentity->user['name'] ?? $eveIdentity->getName();

        if(!$user) {
            if (Character::where('eve_character_id', $eveIdentity->id)->exists()) {
                return redirect(config('app.frontend'))
                    ->with('error', 'This character is already linked to another account.');
            }

            $user = $email
                ? User::firstOrCreate(['email' => $email], [
                    'name' => $eveCharacterName ?: 'EVE Pilot',
                    'password' => bcrypt(str()->random(32)),
                ])
                : User::firstOrCreate(['email' => "eve_{$characterId}@local"], [
                    'name' => $eveCharacterName ?: 'EVE Pilot',
                    'password' => bcrypt(str()->random(32)),
                ]);

        }

        dd($user, $eveIdentity, $validated);

        //check if user exists



        $user = $this->eveUserRepository->createOrUpdateCharacter($user, (int) $characterId, (array)$validated);

        // Log the user in
        Auth::login($user);

        return $user;
    }
}
