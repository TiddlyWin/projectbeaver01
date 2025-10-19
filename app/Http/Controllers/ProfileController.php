<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController
{
    public function me(Request $request)
    {
        $user = $request->user()->load(['characters', 'mainCharacter']);
        return response()->json($user);
    }
}
