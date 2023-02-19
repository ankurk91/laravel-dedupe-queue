<?php

declare(strict_types=1);

namespace Ankurk91\DedupeQueue\Traits;
use Illuminate\Contracts\Queue\Job as JobContract;

trait CreatesJobLockKey
{
    protected function getLockKey(JobContract $job): string
    {
        return $this->getKeyPrefix().$job->uuid();
    }

    protected function getKeyPrefix(): string
    {
        return 'laravel-queue-dedupe:';
    }
}
