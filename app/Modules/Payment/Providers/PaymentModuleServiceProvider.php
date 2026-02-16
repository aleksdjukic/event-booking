<?php

namespace App\Modules\Payment\Providers;

use App\Application\Contracts\Services\PaymentTransactionServiceInterface;
use App\Application\Services\Payment\PaymentTransactionService;
use App\Domain\Payment\Repositories\PaymentIdempotencyRepositoryInterface;
use App\Domain\Payment\Repositories\PaymentRepositoryInterface;
use App\Domain\Payment\Services\PaymentGatewayInterface;
use App\Infrastructure\Payment\PaymentGatewayService;
use App\Infrastructure\Persistence\Eloquent\PaymentIdempotencyRepository;
use App\Infrastructure\Persistence\Eloquent\PaymentRepository;
use Illuminate\Support\ServiceProvider;

class PaymentModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(PaymentIdempotencyRepositoryInterface::class, PaymentIdempotencyRepository::class);
        $this->app->bind(PaymentGatewayInterface::class, PaymentGatewayService::class);
        $this->app->bind(PaymentTransactionServiceInterface::class, PaymentTransactionService::class);
    }
}
