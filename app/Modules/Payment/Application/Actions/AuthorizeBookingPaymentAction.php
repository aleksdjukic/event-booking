<?php

namespace App\Modules\Payment\Application\Actions;

use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Shared\Domain\DomainError;
use App\Modules\Shared\Domain\DomainException;
use App\Modules\User\Domain\Enums\Role;
use App\Modules\User\Domain\Models\User;

class AuthorizeBookingPaymentAction
{
    public function execute(User $user, Booking $booking): void
    {
        if (
            $user->hasRole(Role::CUSTOMER)
            && $booking->{Booking::COL_USER_ID} !== $user->{User::COL_ID}
        ) {
            throw new DomainException(DomainError::FORBIDDEN);
        }
    }
}
