<?php

namespace App\Application\Booking\Actions;

use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Domain\User\Enums\Role;
use App\Domain\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ListBookingsForUserAction
{
    public function __construct(private readonly BookingRepositoryInterface $bookingRepository)
    {
    }

    /**
     * @return LengthAwarePaginator<int, Booking>
     */
    public function execute(User $user): LengthAwarePaginator
    {
        $all = ! $user->hasRole(Role::CUSTOMER);

        return $this->bookingRepository->paginateForUser($user, $all);
    }
}
