<?php

namespace App\Domain\Payment\Policies;

use App\Enums\Role;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        return $userRole === Role::ADMIN->value || $payment->booking->user_id === $user->id;
    }
}
