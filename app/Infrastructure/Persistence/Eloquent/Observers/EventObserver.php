<?php

namespace App\Infrastructure\Persistence\Eloquent\Observers;

use App\Domain\Event\Models\Event;
use App\Domain\Event\Support\EventCache;
use Illuminate\Support\Facades\Cache;

class EventObserver
{
    public function created(Event $event): void
    {
        $this->bumpEventIndexVersion();
    }

    public function updated(Event $event): void
    {
        $this->bumpEventIndexVersion();
    }

    public function deleted(Event $event): void
    {
        $this->bumpEventIndexVersion();
    }

    public function restored(Event $event): void
    {
        $this->bumpEventIndexVersion();
    }

    private function bumpEventIndexVersion(): void
    {
        if (! Cache::has(EventCache::INDEX_VERSION_KEY)) {
            Cache::forever(EventCache::INDEX_VERSION_KEY, 1);
        }

        Cache::increment(EventCache::INDEX_VERSION_KEY);
    }
}
