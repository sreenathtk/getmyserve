<?php

namespace App\Services\Ziwo;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ZiwoApiClient
{
    private string $baseUrl;
    private string $accountName;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->baseUrl     = rtrim((string) config('ziwo.api_url'), '/');
        $this->accountName = (string) config('ziwo.account_name');
        $this->username    = (string) config('ziwo.username');
        $this->password    = (string) config('ziwo.password');
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('get', $path, $query);
    }

    public function post(string $path, array $body = []): array
    {
        return $this->request('post', $path, $body);
    }

    public function delete(string $path): array
    {
        return $this->request('delete', $path, []);
    }

    private function request(string $method, string $path, array $data = []): array
    {
        $response = $this->client()->{$method}("{$this->baseUrl}{$path}", $data);

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        // 401 → refresh token and retry once
        if ($response->status() === 401) {
            $this->refreshToken();
            $response = $this->client()->{$method}("{$this->baseUrl}{$path}", $data);

            if ($response->successful()) {
                return $response->json() ?? [];
            }
        }

        Log::channel('ziwo')->error('ZiwoApiClient: request failed.', [
            'method' => $method,
            'path'   => $path,
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        throw new RuntimeException("ZIWO API error [{$response->status()}]: {$response->body()}");
    }

    private function client(): PendingRequest
    {
        return Http::withToken($this->getToken())
                   ->timeout(15)
                   ->retry(2, 500)
                   ->acceptJson();
    }

    private function getToken(): string
    {
        // Uses file cache (CACHE_STORE=file) — no Redis required
        return Cache::remember('ziwo_access_token', 3300, fn () => $this->fetchToken());
    }

    private function refreshToken(): string
    {
        Cache::forget('ziwo_access_token');
        return $this->getToken();
    }

    private function fetchToken(): string
    {
        $response = Http::timeout(15)->post("{$this->baseUrl}/auth/login", [
            'account'  => $this->accountName,
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('ZIWO authentication failed: ' . $response->body());
        }

        return $response->json('token')
            ?? $response->json('access_token')
            ?? throw new RuntimeException('ZIWO token not found in response.');
    }
}
