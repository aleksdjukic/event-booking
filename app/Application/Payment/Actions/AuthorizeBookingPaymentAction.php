<?php

namespace App\Application\Payment\Actions;

use App\Domain\Booking\Models\Booking;
use App\Domain\Shared\DomainError;
use App\Domain\Shared\DomainException;
use App\Domain\User\Enums\Role;
use App\Domain\User\Models\User;

class AuthorizeBookingPaymentAction
{
    public function execute(User $user, Booking $booking): void
    {
        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        if ($userRole === Role::CUSTOMER->value && $booking->user_id !== $user->id) {
            throw new DomainException(DomainError::FORBIDDEN);
        }
    }
}
