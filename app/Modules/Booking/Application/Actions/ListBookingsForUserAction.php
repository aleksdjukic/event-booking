<?php

namespace App\Modules\Booking\Application\Actions;

use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\Booking\Domain\Repositories\BookingRepositoryInterface;
use App\Modules\User\Domain\Enums\Role;
use App\Modules\User\Domain\Models\User;
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
