<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;

class ProfileController
{
    public function me(UpdateProfileRequest $request): UserResource
    {
        $user = $request->user()->load(['characters', 'mainCharacter']);
        return new UserResource($user);
    }
}
