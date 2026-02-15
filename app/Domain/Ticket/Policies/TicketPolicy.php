<?php

namespace App\Domain\Ticket\Policies;

use App\Domain\User\Enums\Role;
use App\Domain\Event\Models\Event;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;

class TicketPolicy
{
    public function create(User $user, Event $event): bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return $user->hasRole(Role::ORGANIZER) && $event->created_by === $user->id;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return $user->hasRole(Role::ORGANIZER) && $ticket->event->created_by === $user->id;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return $user->hasRole(Role::ORGANIZER) && $ticket->event->created_by === $user->id;
    }
}
