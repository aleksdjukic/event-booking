<?php

namespace Tests\Unit\Services;

use App\Models\Booking;
use App\Services\PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    public function test_process_returns_true_by_default(): void
    {
        $service = new PaymentService();
        $booking = new Booking();

        $this->assertTrue($service->process($booking));
    }

    public function test_process_returns_false_when_force_success_is_false(): void
    {
        $service = new PaymentService();
        $booking = new Booking();

        $this->assertFalse($service->process($booking, false));
    }
}
