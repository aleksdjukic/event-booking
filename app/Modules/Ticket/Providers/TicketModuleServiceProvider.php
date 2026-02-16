<?php

namespace App\Modules\Ticket\Providers;

use App\Application\Contracts\Services\TicketServiceInterface;
use App\Application\Services\Ticket\TicketService;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\TicketRepository;
use Illuminate\Support\ServiceProvider;

class TicketModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(TicketServiceInterface::class, TicketService::class);
    }
}
