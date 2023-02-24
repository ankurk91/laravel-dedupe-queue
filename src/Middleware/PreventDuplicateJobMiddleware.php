<?php

declare(strict_types=1);

namespace Ankurk91\DedupeQueue\Middleware;

use Ankurk91\DedupeQueue\Traits\CreatesJobLockKey;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PreventDuplicateJobMiddleware
{
    use CreatesJobLockKey;

    public function handle(object $task, $next)
    {
        if (!config('dedupe-queue.enabled')) {
            return $next($task);
        }

        $key = $this->getLockKey($task->job);

        if (!Cache::lock($key, $this->getLockDuration())->acquire()) {
            $this->logMessage($task);

            return false;
        }

        return $next($task);
    }

    protected function getLockDuration(): int
    {
        return config('dedupe-queue.lock_duration_in_seconds');
    }

    protected function logMessage(object $task): void
    {
        /** @var JobContract $job */
        $job = $task->job;

        Log::info("Skip processing a duplicate job",
            [
                'uuid' => $job->uuid(),
                'name' => $job->resolveName(),
                'connection' => $job->getConnectionName(),
                'queue' => $job->getQueue(),
            ]
        );
    }
}
