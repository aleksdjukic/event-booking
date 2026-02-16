<?php

namespace App\Modules\Payment\Domain;

use App\Modules\Payment\Domain\Enums\PaymentStatus;

class PaymentTransitionGuard
{
    public function canNotifyCustomer(PaymentStatus $status): bool
    {
        return $status === PaymentStatus::SUCCESS;
    }
}
