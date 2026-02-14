<?php

namespace App\Application\Event\Actions;

use Illuminate\Support\Facades\Cache;

class BumpEventIndexVersionAction
{
    public function execute(): void
    {
        if (! Cache::has('events:index:version')) {
            Cache::forever('events:index:version', 1);
        }

        Cache::increment('events:index:version');
    }
}
