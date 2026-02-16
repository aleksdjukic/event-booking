<?php

namespace App\Modules\Payment\Application\Actions;

use App\Modules\Booking\Domain\Repositories\BookingRepositoryInterface;
use App\Modules\Booking\Infrastructure\Notifications\BookingConfirmedNotification;
use App\Modules\Payment\Domain\PaymentTransitionGuard;
use App\Modules\Payment\Domain\Repositories\PaymentRepositoryInterface;
use App\Modules\Payment\Domain\Services\PaymentGatewayInterface;
use App\Modules\Shared\Domain\DomainError;
use App\Modules\Shared\Domain\DomainException;
use App\Modules\Ticket\Domain\Repositories\TicketRepositoryInterface;
use App\Modules\Payment\Application\DTO\CreatePaymentData;
use App\Modules\Booking\Domain\Enums\BookingStatus;
use App\Modules\Event\Domain\Models\Event;
use App\Modules\Payment\Domain\Enums\PaymentStatus;
use App\Modules\Payment\Domain\Models\Payment;
use App\Modules\Payment\Domain\Models\PaymentIdempotencyKey;
use App\Modules\Ticket\Domain\Models\Ticket;
use App\Modules\Booking\Domain\Models\Booking;
use App\Modules\User\Domain\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ProcessPaymentAction
{
    public function __construct(
        private readonly PaymentGatewayInterface $gatewayService,
        private readonly PaymentTransitionGuard $paymentTransitionGuard,
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly ResolvePaymentIdempotencyAction $resolvePaymentIdempotencyAction,
        private readonly AttachPaymentToIdempotencyRecordAction $attachPaymentToIdempotencyRecordAction,
        private readonly AuthorizeBookingPaymentAction $authorizeBookingPaymentAction,
        private readonly EnsureBookingPayableAction $ensureBookingPayableAction,
        private readonly EnsureTicketInventoryForBookingAction $ensureTicketInventoryForBookingAction,
        private readonly DispatchBookingConfirmedNotificationAction $dispatchBookingConfirmedNotificationAction,
    ) {
    }

    public function execute(User $user, CreatePaymentData $data): Payment
    {
        $idempotencyRecord = $this->resolvePaymentIdempotencyAction->execute($user, $data);
        if ($idempotencyRecord?->{PaymentIdempotencyKey::COL_PAYMENT_ID} !== null) {
            $existingPayment = $this->paymentRepository->findWithBooking(
                (int) $idempotencyRecord->{PaymentIdempotencyKey::COL_PAYMENT_ID}
            );

            if ($existingPayment !== null) {
                return $existingPayment;
            }
        }

        try {
            $notificationPayload = null;
            $payment = DB::transaction(function () use ($data, $user, $idempotencyRecord, &$notificationPayload): Payment {
                $booking = $this->bookingRepository->findForUpdate($data->bookingId);

                if ($booking === null) {
                    throw new DomainException(DomainError::BOOKING_NOT_FOUND);
                }

                $this->authorizeBookingPaymentAction->execute($user, $booking);
                $this->ensureBookingPayableAction->execute($booking);

                $ticket = $this->ticketRepository->findForUpdateWithEvent($booking->{Booking::COL_TICKET_ID});

                if ($ticket === null) {
                    throw new DomainException(DomainError::TICKET_NOT_FOUND);
                }

                $this->ensureTicketInventoryForBookingAction->execute($booking, $ticket);

                $amount = round(
                    ((float) $ticket->{Ticket::COL_PRICE}) * (int) $booking->{Booking::COL_QUANTITY},
                    2
                );
                $processed = $this->gatewayService->process($booking, $data->forceSuccess);

                if ($processed) {
                    $ticket->{Ticket::COL_QUANTITY} = $ticket->{Ticket::COL_QUANTITY} - $booking->{Booking::COL_QUANTITY};
                    $this->ticketRepository->save($ticket);

                    $booking->{Booking::COL_STATUS} = BookingStatus::CONFIRMED;
                    $this->bookingRepository->save($booking);

                    $notificationPayload = [
                        BookingConfirmedNotification::PAYLOAD_BOOKING_ID => (int) $booking->{Booking::COL_ID},
                        BookingConfirmedNotification::PAYLOAD_EVENT_TITLE => $ticket->{Ticket::REL_EVENT}?->{Event::COL_TITLE},
                        BookingConfirmedNotification::PAYLOAD_TICKET_TYPE => $ticket->{Ticket::COL_TYPE},
                        BookingConfirmedNotification::PAYLOAD_QUANTITY => (int) $booking->{Booking::COL_QUANTITY},
                    ];

                    $paymentStatus = PaymentStatus::SUCCESS;
                } else {
                    $booking->{Booking::COL_STATUS} = BookingStatus::CANCELLED;
                    $this->bookingRepository->save($booking);
                    $paymentStatus = PaymentStatus::FAILED;
                }

                $payment = $this->paymentRepository->create($booking, $amount, $paymentStatus);
                if ($idempotencyRecord !== null) {
                    $this->attachPaymentToIdempotencyRecordAction->execute($idempotencyRecord, (int) $payment->{Payment::COL_ID});
                }

                return $payment;
            });

            if ($this->paymentTransitionGuard->canNotifyCustomer($payment->{Payment::COL_STATUS}) && is_array($notificationPayload)) {
                $this->dispatchBookingConfirmedNotificationAction->execute($payment, $notificationPayload);
            }

            return $payment->load(Payment::REL_BOOKING);
        } catch (QueryException $exception) {
            if ($this->isDuplicatePaymentException($exception)) {
                throw new DomainException(DomainError::PAYMENT_ALREADY_EXISTS);
            }

            throw $exception;
        }
    }

    private function isDuplicatePaymentException(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, Payment::TABLE.'_'.Payment::COL_BOOKING_ID.'_unique')) {
            return true;
        }

        $hasBookingIdColumn = str_contains($message, Payment::TABLE.'.'.Payment::COL_BOOKING_ID)
            || str_contains($message, '`'.Payment::COL_BOOKING_ID.'`')
            || str_contains($message, ' '.Payment::COL_BOOKING_ID.' ');
        $hasUniqueHint = str_contains($message, 'unique') || str_contains($message, 'duplicate');

        return $hasBookingIdColumn && $hasUniqueHint;
    }
}
