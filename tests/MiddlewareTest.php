<?php

declare(strict_types=1);

namespace Ankurk91\DedupeQueue\Tests;

use Ankurk91\DedupeQueue\Tests\Fixtures\Jobs\DemoJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MiddlewareTest extends AbstractTestCase
{
    public function test_it_allow_to_dispatch_new_jobs()
    {
        Log::shouldReceive('debug')
            ->times(2)
            ->withArgs(function (string $message) {
                return str_contains($message, 'Running job');
            });

        dispatch(new DemoJob());
        dispatch(new DemoJob());
    }

    public function test_it_does_not_allow_to_run_same_job_twice()
    {
        Str::createUuidsUsing(function () {
            return 'a3d06846-98c4-4e98-87fa-368a9557b10e';
        });

        Log::shouldReceive('debug')
            ->once()
            ->withArgs(function (string $message) {
                return str_contains($message, 'Running job');
            });

        dispatch(new DemoJob());

        Log::shouldReceive('info')
            ->times(2)
            ->withArgs(function (string $message) {
                return str_contains($message, 'Skip processing a duplicate job');
            });

        dispatch(new DemoJob());
        dispatch(new DemoJob());
    }

    public function test_it_bypass_middleware_when_disabled()
    {
        Str::createUuidsUsing(function () {
            return '9cdcf47b-d4d3-4f33-ab88-8e3bb6e3bf12';
        });

        config()->set('dedupe-queue.enabled', false);

        Log::shouldReceive('debug')
            ->times(2)
            ->withArgs(function (string $message) {
                return str_contains($message, 'Running job');
            });

        dispatch(new DemoJob());
        dispatch(new DemoJob());
    }

    public function test_it_allows_to_run_a_failed_job_again()
    {
        Str::createUuidsUsing(function () {
            return '92577bc2-9620-4e63-8d27-c2e369ce2dba';
        });

        Log::shouldReceive('debug')
            ->times(2)
            ->withArgs(function (string $message) {
                return str_contains($message, 'Running job');
            });

        dispatch(new DemoJob(shouldFail: true));

        dispatch(new DemoJob());
    }

    public function test_it_allows_to_rerun_the_released_job()
    {
        Str::createUuidsUsing(function () {
            return 'efa22628-8f5f-4634-bd77-860b05d644fe';
        });

        Log::shouldReceive('debug')
            ->times(2)
            ->withArgs(function (string $message) {
                return str_contains($message, 'Running job');
            });

        Log::shouldReceive('debug')
            ->times(1)
            ->withArgs(function (string $message) {
                return str_contains($message, 'Releasing job');
            });

        dispatch(new DemoJob(shouldRelease: true));
        dispatch(new DemoJob(shouldRelease: false));
    }

    public function test_it_allows_to_rerun_a_job_failed_by_exception()
    {
        Str::createUuidsUsing(function () {
            return '11ec7b9a-76d3-412a-8146-7c9ce3c822c5';
        });

        Log::shouldReceive('debug')
            ->times(2)
            ->withArgs(function (string $message) {
                return str_contains($message, 'Running job');
            });

        try {
            dispatch(new DemoJob(shouldFail: true, failByException: true));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\RuntimeException::class, $e);
            $this->assertStringContainsString('Force fail job', $e->getMessage());
        }

        dispatch(new DemoJob());
    }

    public function test_it_can_handle_closure_based_jobs()
    {
        Str::createUuidsUsing(function () {
            return '11ec7b9a-76d3-412a-8146-7c9ce3c822c5';
        });

        Log::shouldReceive('debug')
            ->times(1)
            ->withArgs(function (string $message) {
                return str_contains($message, 'Running job');
            });

        dispatch(function () {
            Log::debug("Running job");
        });

        Log::shouldReceive('info')
            ->times(1)
            ->withArgs(function (string $message) {
                return str_contains($message, 'Skip processing a duplicate job');
            });

        dispatch(function () {
            Log::debug("Running job");
        });
    }
}
