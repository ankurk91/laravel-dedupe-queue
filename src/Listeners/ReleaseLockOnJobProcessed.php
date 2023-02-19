<?php

declare(strict_types=1);

namespace Ankurk91\DedupeQueue\Listeners;

use Ankurk91\DedupeQueue\Traits\CreatesJobLockKey;
use Illuminate\Support\Facades\Cache;

class ReleaseLockOnJobProcessed
{
    use CreatesJobLockKey;

    public function handle(object $event): void
    {
        if ($event->job->isReleased()) {
            $key = $this->getLockKey($event->job);

            Cache::lock($key)->forceRelease();
        }
    }
}
