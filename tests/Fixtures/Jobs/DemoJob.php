<?php
declare(strict_types=1);

namespace Ankurk91\DedupeQueue\Tests\Fixtures\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DemoJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected bool $shouldFail = false,
        protected bool $failByException = false,
        protected bool $shouldRelease = false
    ) {
        //
    }

    public function handle()
    {
        Log::debug("Running job with uuid: ".$this->job->uuid());

        if ($this->shouldRelease) {
            Log::debug("Releasing job with uuid: ".$this->job->uuid());
            $this->release(5);
            return;
        }

        if ($this->shouldFail) {
            if ($this->failByException) {
                throw new \RuntimeException("Force fail job with uuid: ".$this->job->uuid());
            } else {
                $this->fail("Manually failed job with uuid: ".$this->job->uuid());
            }
        }
    }
}
