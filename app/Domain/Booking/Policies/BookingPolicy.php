<?php

namespace App\Domain\Booking\Policies;

use App\Domain\User\Enums\Role;
use App\Domain\Booking\Models\Booking;
use App\Domain\User\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::CUSTOMER]);
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->hasRole(Role::ADMIN) || $booking->user_id === $user->id;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->hasRole(Role::ADMIN) || $booking->user_id === $user->id;
    }
}
