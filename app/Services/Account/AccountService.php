<?php

namespace App\Services\Account;

use Exception;
use RuntimeException;

class AccountService implements AccountServiceInterface
{
    /**
     * @throws Exception
     */
 public function registerEmail($user, $data): void
    {
        if (!$user->exists || !$user->getKey()) {
            throw new RuntimeException('User must be persisted to register an email.');
        }

        if(isset($data['email'])) {
            $existingUser = $user->where('email', $data['email'])->first();
            $emailVarified = $user->whereNotNull('email_verified_at')->where('email', $data['email'])->first();

            if ($existingUser) {
                throw new RuntimeException('Email is already in use.');
            }

            if ($emailVarified) {
                throw new RuntimeException('Email is already verified by another user.');
            }
        }

        $user->email = $data['email'];
        $user->email_verified_at = now();
        $user->save();
    }
}
