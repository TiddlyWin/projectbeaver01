<?php

namespace App\Services\EveOnline;

use App\Models\Character;
use App\Repositories\EVEUserRepository;
use Exception;
use JsonException;
use Laravel\Socialite\Contracts\User as SocialiteUser;


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
    public function authenticate(SocialiteUser $eveIdentity): Character
    {

        $validated = $this->eveTokenValidator->validate($eveIdentity->token);
        $characterId = $validated['character_id'];

        $user = $this->eveUserRepository->createOrUpdateFromEveLogin((int) $characterId, (array)$eveIdentity);

        // Log the user in

        return $user;
    }
}
