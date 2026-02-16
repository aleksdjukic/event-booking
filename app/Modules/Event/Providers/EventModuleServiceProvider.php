<?php

namespace App\Modules\Event\Providers;

use App\Application\Contracts\Services\EventServiceInterface;
use App\Application\Services\Event\EventService;
use App\Domain\Event\Models\Event;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EventRepository;
use App\Infrastructure\Persistence\Eloquent\Observers\EventObserver;
use Illuminate\Support\ServiceProvider;

class EventModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        $this->app->bind(EventServiceInterface::class, EventService::class);
    }

    public function boot(): void
    {
        Event::observe(EventObserver::class);
    }
}
