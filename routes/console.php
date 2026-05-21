<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Re-queue any ZIWO webhooks that failed processing (max 5 attempts, last 2 days)
Schedule::job(new \App\Jobs\Ziwo\RetryFailedWebhooks)->everyFiveMinutes();

// Reset per-agent daily call stats at midnight
Schedule::call(function () {
    \App\Models\CallAgent::query()->update([
        'total_calls_today'       => 0,
        'total_talk_seconds_today'=> 0,
    ]);
})->dailyAt('00:00');
