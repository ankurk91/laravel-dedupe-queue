<?php
declare(strict_types=1);

namespace Ankurk91\DedupeQueue;

use Ankurk91\DedupeQueue\Listeners\ReleaseLockOnJobFailure;
use Ankurk91\DedupeQueue\Listeners\ReleaseLockOnJobProcessed;
use Ankurk91\DedupeQueue\Middleware\PreventDuplicateJobMiddleware;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobReleasedAfterException;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class DedupeQueueServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->getConfigPath() => config_path('dedupe-queue.php'),
            ], 'config');
        }

        $this->addMiddleware();
        $this->addListeners();
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'dedupe-queue');
    }

    protected function addMiddleware(): void
    {
        Bus::pipeThrough([
            PreventDuplicateJobMiddleware::class,
        ]);
    }

    protected function addListeners(): void
    {
        Event::listen([
            JobFailed::class,
            JobExceptionOccurred::class,
            JobReleasedAfterException::class,
        ], ReleaseLockOnJobFailure::class);

        Event::listen(JobProcessed::class, ReleaseLockOnJobProcessed::class);
    }

    protected function getConfigPath(): string
    {
        return __DIR__.'/../config/dedupe-queue.php';
    }
}
