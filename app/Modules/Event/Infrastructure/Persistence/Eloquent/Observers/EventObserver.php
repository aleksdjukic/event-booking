<?php

namespace App\Modules\Event\Infrastructure\Persistence\Eloquent\Observers;

use App\Modules\Event\Application\Actions\BumpEventIndexVersionAction;
use App\Modules\Event\Domain\Models\Event;

class EventObserver
{
    public function __construct(private readonly BumpEventIndexVersionAction $bumpEventIndexVersionAction)
    {
    }

    public function created(Event $event): void
    {
        $this->bumpEventIndexVersionAction->execute();
    }

    public function updated(Event $event): void
    {
        $this->bumpEventIndexVersionAction->execute();
    }

    public function deleted(Event $event): void
    {
        $this->bumpEventIndexVersionAction->execute();
    }

    public function restored(Event $event): void
    {
        $this->bumpEventIndexVersionAction->execute();
    }
}
