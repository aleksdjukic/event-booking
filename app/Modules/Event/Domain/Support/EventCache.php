<?php

namespace App\Modules\Event\Domain\Support;

final class EventCache
{
    public const INDEX_VERSION_KEY = 'events:index:version';
    public const INDEX_TTL_SECONDS = 120;

    public static function indexPageKey(int $version, int $page): string
    {
        return 'events:index:v'.$version.':page:'.$page;
    }
}
