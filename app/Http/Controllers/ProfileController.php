<?php

namespace App\Http\Controllers;

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
