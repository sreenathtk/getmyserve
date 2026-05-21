<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Ziwo\ProcessZiwoWebhook;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ZiwoWebhookController extends Controller
{
    public function receive(Request $request): Response
    {
        if (! $this->verifySignature($request)) {
            Log::channel('ziwo')->warning('Webhook: invalid signature.', [
                'ip' => $request->ip(),
            ]);
            return response('Forbidden', 403);
        }

        $payload   = $request->all();
        $eventType = $payload['event'] ?? $payload['eventType'] ?? 'unknown';

        $idempotencyKey = $payload['eventId']
            ?? hash('sha256', $eventType . json_encode($payload));

        // Guard against duplicate deliveries
        if (WebhookLog::where('idempotency_key', $idempotencyKey)->exists()) {
            return response('OK', 200);
        }

        $log = WebhookLog::create([
            'source'          => 'ziwo',
            'event_type'      => $eventType,
            'payload'         => $payload,
            'headers'         => $request->headers->all(),
            'signature'       => $request->header('X-Ziwo-Signature'),
            'is_verified'     => true,
            'idempotency_key' => $idempotencyKey,
        ]);

        ProcessZiwoWebhook::dispatch($log->id);

        return response('OK', 200);
    }

    private function verifySignature(Request $request): bool
    {
        $secret    = config('ziwo.webhook_secret');
        $signature = $request->header('X-Ziwo-Signature');

        if (empty($secret) || empty($signature)) {
            // Allow in local dev if no secret configured
            return app()->environment('local');
        }

        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }
}
