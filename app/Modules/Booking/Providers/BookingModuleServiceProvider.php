<?php

namespace App\Modules\Booking\Providers;

use App\Application\Contracts\Services\BookingServiceInterface;
use App\Application\Services\Booking\BookingService;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Policies\BookingPolicy;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\BookingRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class BookingModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(BookingServiceInterface::class, BookingService::class);
    }

    public function boot(): void
    {
        Gate::policy(Booking::class, BookingPolicy::class);
    }
}
