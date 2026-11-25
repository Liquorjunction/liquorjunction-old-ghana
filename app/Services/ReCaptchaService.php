<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReCaptchaService
{
    /**
     * Google reCAPTCHA siteverify endpoint.
     */
    protected const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Default acceptable minimum bot score for v3 (0.0 = bot, 1.0 = human).
     */
    protected const DEFAULT_MIN_SCORE = 0.5;

    /**
     * Determine if Google reCAPTCHA is enabled in configuration / admin settings.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        $status = config('services.recaptcha.status', env('RECAPTCHA_STATUS', 1));
        $siteKey = $this->getSiteKey();
        $secretKey = $this->getSecretKey();

        return (bool) $status && !empty($siteKey) && !empty($secretKey);
    }

    /**
     * Get the configured Site Key.
     *
     * @return string
     */
    public function getSiteKey(): string
    {
        return (string) config('services.recaptcha.site_key', env('RECAPTCHA_SITE_KEY', env('NOCAPTCHA_SITEKEY', '')));
    }

    /**
     * Get the configured Secret Key.
     *
     * @return string
     */
    public function getSecretKey(): string
    {
        return (string) config('services.recaptcha.secret_key', env('RECAPTCHA_SECRET_KEY', env('NOCAPTCHA_SECRET', '')));
    }

    /**
     * Verify a reCAPTCHA v3 response token with Google.
     *
     * @param string|null $token The g-recaptcha-response token from frontend
     * @param string|null $expectedAction The expected action name (e.g., 'register', 'login')
     * @param string|null $ip The client IP address
     * @param float|null $minScore The minimum required score (default 0.5)
     * @return array ['success' => bool, 'score' => float, 'message' => string, 'bypassed' => bool]
     */
    public function verify(?string $token, ?string $expectedAction = null, ?string $ip = null, ?float $minScore = null): array
    {
        // 1. If reCAPTCHA is not enabled, bypass verification automatically
        if (!$this->isEnabled()) {
            return [
                'success' => true,
                'score' => 1.0,
                'bypassed' => true,
                'message' => 'reCAPTCHA verification bypassed (inactive).'
            ];
        }

        // 2. Validate token presence
        $token = trim((string) $token);
        if (empty($token)) {
            Log::warning('reCAPTCHA verification failed: Token is empty', [
                'action' => $expectedAction,
                'ip' => $ip ?? request()->ip(),
            ]);

            return [
                'success' => false,
                'score' => 0.0,
                'bypassed' => false,
                'message' => 'reCAPTCHA verification token is missing. Please refresh and try again.'
            ];
        }

        $minScore = $minScore ?? self::DEFAULT_MIN_SCORE;
        $secretKey = $this->getSecretKey();

        try {
            $response = Http::asForm()->timeout(5)->post(self::VERIFY_URL, [
                'secret'   => $secretKey,
                'response' => $token,
                'remoteip' => $ip ?? request()->ip(),
            ]);

            if (!$response->successful()) {
                Log::error('reCAPTCHA Google API HTTP error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'score' => 0.0,
                    'bypassed' => false,
                    'message' => 'Unable to verify reCAPTCHA with Google servers. Please try again.'
                ];
            }

            $data = $response->json();

            // Check if Google verified the token
            if (empty($data['success'])) {
                Log::warning('reCAPTCHA token invalid or expired', [
                    'error_codes' => $data['error-codes'] ?? [],
                    'action' => $expectedAction,
                    'ip' => $ip ?? request()->ip()
                ]);

                return [
                    'success' => false,
                    'score' => 0.0,
                    'bypassed' => false,
                    'message' => 'reCAPTCHA token is invalid or has expired. Please try again.'
                ];
            }

            $score = isset($data['score']) ? (float) $data['score'] : 1.0;
            $action = $data['action'] ?? null;

            // Score threshold validation
            if ($score < $minScore) {
                Log::warning('reCAPTCHA score below threshold (bot detected)', [
                    'score' => $score,
                    'min_score' => $minScore,
                    'action' => $action,
                    'expected_action' => $expectedAction,
                    'ip' => $ip ?? request()->ip(),
                ]);

                return [
                    'success' => false,
                    'score' => $score,
                    'bypassed' => false,
                    'message' => 'Security check failed. Suspicious activity detected.'
                ];
            }

            // Optional action name consistency check
            if ($expectedAction !== null && $action !== null && strtolower($action) !== strtolower($expectedAction)) {
                Log::warning('reCAPTCHA action mismatch', [
                    'action' => $action,
                    'expected_action' => $expectedAction,
                    'ip' => $ip ?? request()->ip()
                ]);
                // We allow a warning rather than hard blocking if action mismatch occurs, or soft match
            }

            return [
                'success' => true,
                'score' => $score,
                'bypassed' => false,
                'action' => $action,
                'message' => 'reCAPTCHA verified successfully.'
            ];

        } catch (\Throwable $e) {
            Log::error('reCAPTCHA verification exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // If Google API connection fails (timeout, network down), we can fail-open or fail-safe.
            // In local/testing or unstable network, fail-open with logging so real users are never blocked by network glitches.
            return [
                'success' => true,
                'score' => 0.5,
                'bypassed' => true,
                'message' => 'reCAPTCHA verification skipped due to connection error.'
            ];
        }
    }
}
