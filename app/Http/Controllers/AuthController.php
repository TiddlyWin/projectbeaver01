<?php

namespace App\Http\Controllers;

use App\Services\EveOnline\EveAuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

use Throwable;

class AuthController
{
    public function __construct(
        protected EveAuthenticationService $eveAuth)
    {

    }

    /**
     * @throws Throwable
     */
    public function redirectToEve()
    {
        return Socialite::driver('eveonline')->redirect();
    }

    /**
     * @throws Throwable
     */
    public function handleEveCallback(): RedirectResponse
    {
        try {
            $eveIdentity = Socialite::driver('eveonline')->user();
            $result = $this->eveAuth->authenticate($eveIdentity);
            dd($result);
            $user = $result->getData();
            dd($user);
            $needsEmail = $result->getData('needs_email');

            return redirect(config('app.auth_frontend'));
        } catch (Throwable $e) {
            report($e);
            return redirect(config('app.frontend') . '?error=auth_failed');
        }
    }

    public function logout(): \Illuminate\Http\Response
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return response()->noContent();
    }
}
