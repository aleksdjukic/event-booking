<?php

namespace App\Services\Payment;

use App\Enums\Role;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Services\Support\ServiceException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PaymentTransactionService
{
    public function __construct(private readonly PaymentGatewayService $gatewayService)
    {
    }

    public function find(int $id): ?Payment
    {
        return Payment::query()->with('booking')->find($id);
    }

    public function process(User $user, int $bookingId, ?bool $forceSuccess = null): Payment
    {
        $notificationPayload = null;

        DB::beginTransaction();

        try {
            $booking = Booking::query()->whereKey($bookingId)->lockForUpdate()->first();

            if ($booking === null) {
                throw new ServiceException('Booking not found.', 404);
            }

            $this->ensureCanProcess($user, $booking);
            $this->ensureBookingCanBePaid($booking);

            $ticket = Ticket::query()->whereKey($booking->ticket_id)->lockForUpdate()->first();

            if ($ticket === null) {
                throw new ServiceException('Ticket not found.', 404);
            }

            $this->ensureInventory($booking, $ticket);

            $amount = number_format(((float) $ticket->price) * (int) $booking->quantity, 2, '.', '');
            $processed = $this->gatewayService->process($booking, $forceSuccess);

            $payment = new Payment();
            $payment->booking_id = $booking->id;
            $payment->amount = $amount;

            if ($processed) {
                $ticket->quantity = $ticket->quantity - $booking->quantity;
                $ticket->save();

                $booking->status = 'confirmed';
                $booking->save();

                $notificationPayload = [
                    'booking_id' => $booking->id,
                    'event_title' => Event::query()->whereKey($ticket->event_id)->value('title'),
                    'ticket_type' => $ticket->type,
                    'quantity' => (int) $booking->quantity,
                ];

                $payment->status = 'success';
            } else {
                $booking->status = 'cancelled';
                $booking->save();
                $payment->status = 'failed';
            }

            $payment->save();
            DB::commit();

            if ($payment->status === 'success' && is_array($notificationPayload)) {
                $booking->load('user');
                $booking->user?->notify(new BookingConfirmedNotification(
                    $notificationPayload['booking_id'],
                    $notificationPayload['event_title'],
                    $notificationPayload['ticket_type'],
                    $notificationPayload['quantity'],
                ));
            }

            return $payment->load('booking');
        } catch (ServiceException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (QueryException $exception) {
            DB::rollBack();

            if ($this->isDuplicatePaymentException($exception)) {
                throw new ServiceException('Payment already exists for this booking.', 409);
            }

            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function ensureCanProcess(User $user, Booking $booking): void
    {
        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        if ($userRole === Role::CUSTOMER->value && $booking->user_id !== $user->id) {
            throw new ServiceException('Forbidden', 403);
        }
    }

    private function ensureBookingCanBePaid(Booking $booking): void
    {
        if ($booking->status !== 'pending') {
            throw new ServiceException('Invalid booking state for payment.', 409);
        }

        $paymentExists = Payment::query()->where('booking_id', $booking->id)->exists();
        if ($paymentExists) {
            throw new ServiceException('Payment already exists for this booking.', 409);
        }
    }

    private function ensureInventory(Booking $booking, Ticket $ticket): void
    {
        if ($ticket->quantity <= 0) {
            throw new ServiceException('Ticket is sold out.', 409);
        }

        if ($booking->quantity > $ticket->quantity) {
            throw new ServiceException('Not enough ticket inventory.', 409);
        }
    }

    private function isDuplicatePaymentException(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'payments_booking_id_unique')) {
            return true;
        }

        $hasBookingIdColumn = str_contains($message, 'payments.booking_id')
            || str_contains($message, '`booking_id`')
            || str_contains($message, ' booking_id ');
        $hasUniqueHint = str_contains($message, 'unique') || str_contains($message, 'duplicate');

        return $hasBookingIdColumn && $hasUniqueHint;
    }
}
