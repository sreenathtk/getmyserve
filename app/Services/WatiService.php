<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WatiService
{
    private string $endpoint;
    private string $token;

    public function __construct()
    {
        $this->endpoint = rtrim((string) config('wati.endpoint'), '/');
        $this->token    = (string) config('wati.token');
    }

    /**
     * Send a WATI template message to a WhatsApp number.
     *
     * @param  string  $phone      International format, digits only (e.g. "971501234567")
     * @param  string  $template   Template name as registered in WATI dashboard
     * @param  array   $parameters Ordered list of parameter values for {{1}}, {{2}}, …
     */
    public function sendTemplate(string $phone, string $template, array $parameters): bool
    {
        $phone = $this->normalisePhone($phone);

        if (empty($phone) || empty($this->endpoint) || empty($this->token)) {
            Log::warning('WatiService: missing phone or credentials, message skipped.', [
                'template' => $template,
                'phone'    => $phone,
            ]);
            return false;
        }

        $body = [
            'template_name'  => $template,
            'broadcast_name' => 'getmyserve',
            'parameters'     => array_values(array_map(
                fn(int $i, string $value) => ['name' => (string) ($i + 1), 'value' => $value],
                array_keys($parameters),
                array_values($parameters)
            )),
        ];

        try {
            $response = Http::withToken($this->token)
                ->timeout(15)
                ->post("{$this->endpoint}/api/v1/sendTemplateMessage?whatsappNumber={$phone}", $body);

            if ($response->successful()) {
                return true;
            }

            Log::error('WatiService: API error.', [
                'template' => $template,
                'phone'    => $phone,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WatiService: HTTP exception.', [
                'template'  => $template,
                'phone'     => $phone,
                'exception' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Fetch all message templates from WATI, keyed by elementName.
     *
     * @return array<string, array>  Keys are WATI element names, values are the raw template objects.
     */
    public function getTemplates(): array
    {
        if (empty($this->endpoint) || empty($this->token)) {
            Log::warning('WatiService::getTemplates: missing credentials.');
            return [];
        }

        try {
            $response = Http::withToken($this->token)
                ->timeout(20)
                ->get("{$this->endpoint}/api/v1/getMessageTemplates");

            if (! $response->successful()) {
                Log::error('WatiService::getTemplates: API error.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return [];
            }

            $data      = $response->json();
            $templates = $data['messageTemplates'] ?? [];

            return collect($templates)
                ->keyBy('elementName')
                ->toArray();
        } catch (\Throwable $e) {
            Log::error('WatiService::getTemplates: exception.', ['exception' => $e->getMessage()]);
            return [];
        }
    }

    private function normalisePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
