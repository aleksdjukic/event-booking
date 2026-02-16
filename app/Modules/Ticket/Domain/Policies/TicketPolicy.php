<?php

namespace App\Modules\Ticket\Domain\Policies;

use App\Modules\User\Domain\Enums\Role;
use App\Modules\Event\Domain\Models\Event;
use App\Modules\Ticket\Domain\Models\Ticket;
use App\Modules\User\Domain\Models\User;

class TicketPolicy
{
    public function create(User $user, Event $event): bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return $user->hasRole(Role::ORGANIZER)
            && $event->{Event::COL_CREATED_BY} === $user->{User::COL_ID};
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return $user->hasRole(Role::ORGANIZER)
            && $ticket->{Ticket::REL_EVENT}->{Event::COL_CREATED_BY} === $user->{User::COL_ID};
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return $user->hasRole(Role::ORGANIZER)
            && $ticket->{Ticket::REL_EVENT}->{Event::COL_CREATED_BY} === $user->{User::COL_ID};
    }
}
