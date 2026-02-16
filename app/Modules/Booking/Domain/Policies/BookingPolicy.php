<?php

namespace App\Modules\Booking\Domain\Policies;

use App\Modules\User\Domain\Enums\Role;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\User\Domain\Models\User;

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
