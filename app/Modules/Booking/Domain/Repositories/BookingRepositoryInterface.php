<?php

namespace App\Modules\Booking\Domain\Repositories;

use App\Modules\Booking\Domain\Enums\BookingStatus;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\User\Domain\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface BookingRepositoryInterface
{
    public function find(int $id): ?Booking;

    public function findForUpdate(int $id): ?Booking;

    public function create(User $user, int $ticketId, int $quantity, BookingStatus $status): Booking;

    /**
     * @return LengthAwarePaginator<int, Booking>
     */
    public function paginateForUser(User $user, bool $all): LengthAwarePaginator;

    public function save(Booking $booking): Booking;
}
