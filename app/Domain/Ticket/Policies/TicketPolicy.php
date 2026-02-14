<?php

namespace App\Domain\Ticket\Policies;

use App\Enums\Role;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function create(User $user, Event $event): bool
    {
        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        if ($userRole === Role::ADMIN->value) {
            return true;
        }

        return $userRole === Role::ORGANIZER->value && $event->created_by === $user->id;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        if ($userRole === Role::ADMIN->value) {
            return true;
        }

        return $userRole === Role::ORGANIZER->value && $ticket->event->created_by === $user->id;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        if ($userRole === Role::ADMIN->value) {
            return true;
        }

        return $userRole === Role::ORGANIZER->value && $ticket->event->created_by === $user->id;
    }
}
