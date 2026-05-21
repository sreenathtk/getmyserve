<?php

namespace App\Jobs\Ziwo;

use App\Models\WebhookLog;
use App\Services\Ziwo\ZiwoWebhookProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessZiwoWebhook implements ShouldQueue
{
    use Queueable;

    public int   $tries   = 5;
    public array $backoff  = [30, 60, 120, 300, 600];

    public function __construct(private readonly int $webhookLogId)
    {
        $this->onQueue('webhooks');
    }

    public function handle(ZiwoWebhookProcessor $processor): void
    {
        $log = WebhookLog::find($this->webhookLogId);

        if (! $log || $log->is_processed) {
            return;
        }

        $processor->process($log);
    }

    public function failed(\Throwable $e): void
    {
        Log::channel('ziwo')->error('ProcessZiwoWebhook permanently failed.', [
            'webhook_log_id' => $this->webhookLogId,
            'error'          => $e->getMessage(),
        ]);
    }
}
