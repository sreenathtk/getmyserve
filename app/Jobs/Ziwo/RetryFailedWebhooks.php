<?php

namespace App\Jobs\Ziwo;

use App\Models\WebhookLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RetryFailedWebhooks implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        WebhookLog::ziwo()
            ->unprocessed()
            ->where('created_at', '>', now()->subDays(2))
            ->each(fn ($log) => ProcessZiwoWebhook::dispatch($log->id));
    }
}
