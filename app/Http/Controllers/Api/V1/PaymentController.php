<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PaymentStoreRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use App\Services\PaymentService;
use App\Support\Http\ApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly PaymentService $paymentService)
    {
    }

    public function store(int $id, PaymentStoreRequest $request): JsonResponse
    {
        $forceSuccess = $request->boolean('force_success', true);

        DB::beginTransaction();

        try {
            $booking = Booking::query()->whereKey($id)->lockForUpdate()->first();

            if ($booking === null) {
                DB::rollBack();

                return $this->error('Booking not found.', 404);
            }

            if ($booking->status !== 'pending') {
                DB::rollBack();

                return $this->error('Invalid booking state for payment.', 409);
            }

            $paymentExists = Payment::query()->where('booking_id', $booking->id)->exists();
            if ($paymentExists) {
                DB::rollBack();

                return $this->error('Payment already exists for this booking.', 409);
            }

            $ticket = Ticket::query()->whereKey($booking->ticket_id)->lockForUpdate()->first();

            if ($ticket === null) {
                DB::rollBack();

                return $this->error('Ticket not found.', 404);
            }

            if ($ticket->quantity <= 0) {
                DB::rollBack();

                return $this->error('Ticket is sold out.', 409);
            }

            if ($booking->quantity > $ticket->quantity) {
                DB::rollBack();

                return $this->error('Not enough ticket inventory.', 409);
            }

            $amount = number_format(((float) $ticket->price) * (int) $booking->quantity, 2, '.', '');
            $processed = $this->paymentService->process($booking, $forceSuccess);

            if ($processed) {
                $ticket->quantity = $ticket->quantity - $booking->quantity;
                $ticket->save();

                $booking->status = 'confirmed';
                $booking->save();

                $payment = new Payment();
                $payment->booking_id = $booking->id;
                $payment->amount = $amount;
                $payment->status = 'success';
                $payment->save();
            } else {
                $booking->status = 'cancelled';
                $booking->save();

                $payment = new Payment();
                $payment->booking_id = $booking->id;
                $payment->amount = $amount;
                $payment->status = 'failed';
                $payment->save();
            }

            DB::commit();

            return $this->created($payment->load('booking'), 'Payment processed successfully');
        } catch (QueryException $exception) {
            DB::rollBack();

            if ($this->isDuplicatePaymentException($exception)) {
                return $this->error('Payment already exists for this booking.', 409);
            }

            throw $exception;
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function show(int $id): JsonResponse
    {
        $payment = Payment::query()->with('booking')->find($id);

        if ($payment === null) {
            return $this->error('Payment not found.', 404);
        }

        $this->authorize('view', $payment);

        return $this->success($payment, 'OK');
    }

    private function isDuplicatePaymentException(QueryException $exception): bool
    {
        $message = $exception->getMessage();

        return str_contains($message, 'payments_booking_id_unique')
            || str_contains($message, 'payments.booking_id')
            || (string) $exception->getCode() === '23000';
    }
}
