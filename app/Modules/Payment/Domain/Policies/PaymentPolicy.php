<?php

namespace App\Modules\Payment\Domain\Policies;

use App\Modules\User\Domain\Enums\Role;
use App\Modules\Payment\Domain\Models\Payment;
use App\Modules\User\Domain\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $user->hasRole(Role::ADMIN) || $payment->booking->user_id === $user->id;
    }
}
