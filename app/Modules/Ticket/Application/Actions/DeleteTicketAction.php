<?php

namespace App\Modules\Ticket\Application\Actions;

use App\Modules\Event\Application\Actions\BumpEventIndexVersionAction;
use App\Modules\Ticket\Domain\Models\Ticket;
use App\Modules\Ticket\Domain\Repositories\TicketRepositoryInterface;

class DeleteTicketAction
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly BumpEventIndexVersionAction $bumpEventIndexVersion,
    ) {
    }

    public function execute(Ticket $ticket): void
    {
        $this->ticketRepository->delete($ticket);
        $this->bumpEventIndexVersion->execute();
    }
}
