<?php

namespace App\Modules\Booking\Infrastructure\Persistence\Eloquent;

use App\Modules\Booking\Domain\Repositories\BookingRepositoryInterface;
use App\Modules\Booking\Domain\Enums\BookingStatus;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\User\Domain\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingRepository implements BookingRepositoryInterface
{
    public function find(int $id): ?Booking
    {
        return Booking::query()->find($id);
    }

    public function findForUpdate(int $id): ?Booking
    {
        return Booking::query()->whereKey($id)->lockForUpdate()->first();
    }

    public function create(User $user, int $ticketId, int $quantity, BookingStatus $status): Booking
    {
        $booking = new Booking();
        $booking->{Booking::COL_USER_ID} = $user->{User::COL_ID};
        $booking->{Booking::COL_TICKET_ID} = $ticketId;
        $booking->{Booking::COL_QUANTITY} = $quantity;
        $booking->{Booking::COL_STATUS} = $status;
        $booking->save();

        return $booking;
    }

    /**
     * @return LengthAwarePaginator<int, Booking>
     */
    public function paginateForUser(User $user, bool $all): LengthAwarePaginator
    {
        $query = Booking::query()->with([Booking::REL_TICKET, Booking::REL_PAYMENT]);

        if (! $all) {
            $query->where(Booking::COL_USER_ID, $user->{User::COL_ID});
        }

        return $query->paginate();
    }

    public function save(Booking $booking): Booking
    {
        $booking->save();

        return $booking;
    }
}
