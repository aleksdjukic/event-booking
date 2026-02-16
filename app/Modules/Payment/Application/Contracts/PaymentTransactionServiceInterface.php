<?php

namespace App\Modules\Payment\Application\Contracts;

use App\Modules\Payment\Application\DTO\CreatePaymentData;
use App\Modules\Payment\Domain\Models\Payment;
use App\Modules\User\Domain\Models\User;

interface PaymentTransactionServiceInterface
{
    public function process(User $user, CreatePaymentData $data): Payment;
}
