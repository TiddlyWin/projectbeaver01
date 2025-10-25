<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\RegisterEmailRequest;
use App\Services\Account\AccountServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    protected AccountServiceInterface $accountService;

    public function __construct(AccountServiceInterface $accountService){
        $this->accountService = $accountService;
    }

    public function registerEmail(RegisterEmailRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            $this->accountService->registerEmail($user, $request->validated());
            return response()->json([
                'message' => 'Email registered successfully.'
            ]);

        } catch (Exception $e) {
            dump($e);
            report($e);
            abort(500, 'Unable to register email at this time.');
        }


    }

}
