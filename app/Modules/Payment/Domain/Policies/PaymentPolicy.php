<?php

namespace App\Modules\Payment\Domain\Policies;

use App\Modules\User\Domain\Enums\Role;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Payment\Domain\Models\Payment;
use App\Modules\User\Domain\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $user->hasRole(Role::ADMIN)
            || $payment->{Payment::REL_BOOKING}->{Booking::COL_USER_ID} === $user->{User::COL_ID};
    }
}
