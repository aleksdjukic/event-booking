<?php

namespace App\Modules\Booking\Providers;

use App\Application\Contracts\Services\BookingServiceInterface;
use App\Application\Services\Booking\BookingService;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\BookingRepository;
use Illuminate\Support\ServiceProvider;

class BookingModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(BookingServiceInterface::class, BookingService::class);
    }
}
