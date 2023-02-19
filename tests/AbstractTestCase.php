<?php
declare(strict_types=1);

namespace Ankurk91\DedupeQueue\Tests;

use Ankurk91\DedupeQueue\DedupeQueueServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class AbstractTestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            DedupeQueueServiceProvider::class,
        ];
    }
}
