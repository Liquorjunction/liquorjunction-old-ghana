<?php

namespace App\Services;

use App\Models\Transactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class MojoPay
{
    public function createCheckoutSession(string $amount, string $merchantReference, string $description): array
    {
        $payload = [
            'amount_mode' => 'fixed',
            'currency' => 'GHS',
            'amount' => $this->formatAmount($amount),
            'merchant_reference' => $merchantReference,
            'description' => $description,
            'success_url' => route('mojo.return', ['order' => $merchantReference]),
            'cancel_url' => route('mojo.cancel', ['order' => $merchantReference]),
        ];

        $response = Http::withToken($this->accessToken())
            ->withHeaders(['Idempotency-Key' => (string) Str::uuid()])
            ->acceptJson()
            ->timeout(30)
            ->post($this->endpoint('/checkout/sessions'), $payload);

        $url = $response->json('url');
        $sessionId = (string) $response->json('id');
        if (!$response->successful() || $url === null || $url === '' || $sessionId === '') {
            logger()->warning('Mojo checkout session failed', [
                'reference' => $merchantReference,
                'status' => $response->status(),
                'error' => $response->json('error') ?? $response->json('message'),
            ]);
            throw new RuntimeException('Unable to start Mojo Pay checkout.');
        }

        return [
            'url' => $url,
            'id' => $sessionId,
        ];
    }

    public function rememberSession(int $orderId, string $sessionId): void
    {
        Cache::put($this->sessionCacheKey($orderId), $sessionId, now()->addHours(48));

        try {
            $this->ensureSessionColumn();
            Transactions::where('order_id', $orderId)->update(['mojo_session_id' => $sessionId]);
        } catch (\Throwable $exception) {
            logger()->warning('Could not store Mojo session id', [
                'order_id' => $orderId,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function sessionIdForOrder(int $orderId): ?string
    {
        $cached = Cache::get($this->sessionCacheKey($orderId));
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        try {
            if (!Schema::hasColumn('transactions', 'mojo_session_id')) {
                return null;
            }
            $stored = Transactions::where('order_id', $orderId)->value('mojo_session_id');
            return is_string($stored) && $stored !== '' ? $stored : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public function sessionStatus(string $sessionId): string
    {
        $response = Http::withToken($this->accessToken())
            ->acceptJson()
            ->timeout(30)
            ->get($this->endpoint('/checkout/sessions/' . rawurlencode($sessionId)));

        if (!$response->successful()) {
            logger()->warning('Mojo session status failed', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'error' => $response->json('error') ?? $response->json('message'),
            ]);
            return 'unknown';
        }

        return $this->normalizeStatus($response->json());
    }

    public function verifyWebhook(string $payload, ?string $header): bool
    {
        $secret = (string) config('services.mojopay.webhook_secret');
        if ($secret === '' || !preg_match('/^t=(\d+),v1=([a-f0-9]{64})$/', (string) $header, $matches)) {
            return false;
        }

        $timestamp = (int) $matches[1];
        if (abs(time() - $timestamp) > 300) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        return hash_equals($expected, $matches[2]);
    }

    private function accessToken(): string
    {
        $cached = Cache::get('mojopay.access_token');
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $clientId = (string) config('services.mojopay.client_id');
        $clientSecret = (string) config('services.mojopay.client_secret');
        if ($clientId === '' || $clientSecret === '') {
            throw new RuntimeException('Mojo Pay credentials are not configured.');
        }

        $response = Http::acceptJson()
            ->timeout(30)
            ->post($this->endpoint('/oauth/token'), [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

        $token = $response->json('access_token');
        if (!$response->successful() || empty($token)) {
            logger()->warning('Mojo token request failed', ['status' => $response->status()]);
            throw new RuntimeException('Unable to authenticate with Mojo Pay.');
        }

        $expiresIn = (int) ($response->json('expires_in') ?: 1200);
        Cache::put('mojopay.access_token', $token, max(60, $expiresIn - 60));

        return $token;
    }

    private function endpoint(string $path): string
    {
        return rtrim((string) config('services.mojopay.base_url'), '/') . $path;
    }

    private function formatAmount(string $amount): string
    {
        $normalized = str_replace(',', '', $amount);

        return number_format((float) $normalized, 2, '.', '');
    }

    private function normalizeStatus($body): string
    {
        $status = '';
        if (is_array($body)) {
            $status = (string) (
                $body['status']
                ?? ($body['data']['object']['status'] ?? null)
                ?? ($body['data']['status'] ?? null)
                ?? ''
            );
        }

        $status = strtolower($status);
        if (in_array($status, ['succeeded', 'success', 'paid', 'approved'], true)) {
            return 'succeeded';
        }
        if (in_array($status, ['failed', 'cancelled', 'canceled', 'expired'], true)) {
            return 'failed';
        }

        return $status === '' ? 'unknown' : 'pending';
    }

    private function sessionCacheKey(int $orderId): string
    {
        return 'mojopay.session.' . $orderId;
    }

    private function ensureSessionColumn(): void
    {
        if (Schema::hasColumn('transactions', 'mojo_session_id')) {
            return;
        }

        Schema::table('transactions', function ($table) {
            $table->string('mojo_session_id', 64)->nullable();
        });
    }
}
