<?php

use Carbon\Carbon;

return [
    /**
     * Enable/disable the global middleware
     */
    'enabled' => (bool) env('DEDUPE_QUEUE_ENABLED', true),

    /**
     * Amount of seconds you want to prevent a job from being duplicate
     */
    'lock_duration_in_seconds' => Carbon::SECONDS_PER_MINUTE * 60
];
