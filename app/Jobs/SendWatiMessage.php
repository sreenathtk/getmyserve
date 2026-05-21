<?php

namespace App\Jobs;

use App\Services\WatiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWatiMessage implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public array $backoff = [60, 300]; // retry after 1 min, then 5 min

    public function __construct(
        private readonly string $phone,
        private readonly string $templateName,
        private readonly array  $parameters,
    ) {}

    public function handle(WatiService $wati): void
    {
        $wati->sendTemplate($this->phone, $this->templateName, $this->parameters);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendWatiMessage job permanently failed.', [
            'template'  => $this->templateName,
            'phone'     => $this->phone,
            'exception' => $exception->getMessage(),
        ]);
    }
}
