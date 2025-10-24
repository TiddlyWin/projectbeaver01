<?php

namespace App\Services\Account;

use App\Models\User;

interface AccountServiceInterface
{
    public function registerEmail(User $user, array $data): void;

}
