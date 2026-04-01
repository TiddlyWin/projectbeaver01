<?php

namespace App\Http\Controllers;

use App\Services\EveOnline\EveCharacterAuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

use Throwable;

class AuthController extends Controller
{
    public function __construct(
        protected EveCharacterAuthenticationService $eveAuth
    ){
    }

    /**
     * @throws Throwable
     */
    public function redirectToEve(): RedirectResponse
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

            $data = $result->getData();
            $needsEmail = !empty($data->needs_email);

            if($needsEmail) {
                return redirect()->intended(config('app.register_email_uri'));
            }

            return redirect()->intended(config('app.auth_frontend'));
        } catch (ValidationException $e) {
            return $this->redirectWithError('validation_error', $e->getMessage());
        } catch (Throwable $e) {
            report($e);
            return $this->redirectWithError('auth_failed', $e->getMessage());
        }
    }

    /**
     * Create a redirect response with error parameters
     */
    private function redirectWithError(string $errorType, string $message): RedirectResponse
    {
        return redirect(config('app.frontend') . '?' . http_build_query([
                'error' => $errorType,
                'message' => $message
            ]));
    }

    public function logout(): Response
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return response()->noContent();
    }
}
