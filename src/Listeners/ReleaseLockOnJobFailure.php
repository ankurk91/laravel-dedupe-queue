<?php

declare(strict_types=1);

namespace Ankurk91\DedupeQueue\Listeners;

use Ankurk91\DedupeQueue\Traits\CreatesJobLockKey;
use Illuminate\Support\Facades\Cache;

class ReleaseLockOnJobFailure
{
    use CreatesJobLockKey;

    public function handle(object $event): void
    {
        $key = $this->getLockKey($event->job);

        Cache::lock($key)->forceRelease();
    }
}
