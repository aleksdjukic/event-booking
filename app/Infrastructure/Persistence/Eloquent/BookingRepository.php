<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\User\Models\User;
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
        $booking->{Booking::COL_USER_ID} = $user->id;
        $booking->{Booking::COL_TICKET_ID} = $ticketId;
        $booking->{Booking::COL_QUANTITY} = $quantity;
        $booking->{Booking::COL_STATUS} = $status;
        $booking->{Booking::COL_ACTIVE_BOOKING_KEY} = $this->makeActiveBookingKey($user->id, $ticketId, $status);
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
            $query->where(Booking::COL_USER_ID, $user->id);
        }

        return $query->paginate();
    }

    public function save(Booking $booking): Booking
    {
        $status = $booking->status instanceof BookingStatus
            ? $booking->status
            : BookingStatus::from((string) $booking->status);

        $booking->{Booking::COL_ACTIVE_BOOKING_KEY} = $this->makeActiveBookingKey(
            (int) $booking->{Booking::COL_USER_ID},
            (int) $booking->{Booking::COL_TICKET_ID},
            $status
        );
        $booking->save();

        return $booking;
    }

    private function makeActiveBookingKey(int $userId, int $ticketId, BookingStatus $status): ?string
    {
        return $status->isActive() ? $userId.':'.$ticketId : null;
    }
}
