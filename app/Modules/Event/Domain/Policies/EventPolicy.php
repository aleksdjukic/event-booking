<?php

namespace App\Modules\Event\Domain\Policies;

use App\Modules\User\Domain\Enums\Role;
use App\Modules\Event\Domain\Models\Event;
use App\Modules\User\Domain\Models\User;

class EventPolicy
{
    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function view(?User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::ORGANIZER]);
    }

    public function update(User $user, Event $event): bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return $user->hasRole(Role::ORGANIZER) && $event->created_by === $user->id;
    }

    public function delete(User $user, Event $event): bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return $user->hasRole(Role::ORGANIZER) && $event->created_by === $user->id;
    }
}
