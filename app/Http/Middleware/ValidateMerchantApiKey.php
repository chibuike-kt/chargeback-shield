<?php

namespace App\Http\Middleware;

use App\Http\Resources\Api\ApiResponse;
use App\Models\Merchant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class ValidateMerchantApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key')
            ?? $request->header('Authorization')
            ?? $request->query('api_key');

        if ($apiKey && str_starts_with($apiKey, 'Bearer ')) {
            $apiKey = substr($apiKey, 7);
        }

        if (!$apiKey) {
            $this->trackFailedAttempt($request->ip());
            return ApiResponse::error(
                'API key is required. Pass it as X-API-Key header.',
                401
            );
        }

        // Check if this IP is temporarily blocked
        if ($this->isBlocked($request->ip())) {
            return ApiResponse::error(
                'Too many failed authentication attempts. Try again in 5 minutes.',
                429
            );
        }

        $merchant = Merchant::where('api_key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$merchant) {
            $this->trackFailedAttempt($request->ip());
            return ApiResponse::error('Invalid or inactive API key.', 401);
        }

        // Clear failed attempts on successful auth
        $this->clearFailedAttempts($request->ip());

        $request->attributes->set('merchant', $merchant);

        return $next($request);
    }

    private function trackFailedAttempt(string $ip): void
    {
        try {
            $key = "auth:failed:{$ip}";
            Redis::incr($key);
            Redis::expire($key, 300); // 5 minute window
        } catch (\Exception $e) {
            // Non-fatal
        }
    }

    private function isBlocked(string $ip): bool
    {
        try {
            $count = (int) Redis::get("auth:failed:{$ip}");
            return $count >= 20; // Block after 20 failed attempts
        } catch (\Exception $e) {
            return false; // Fail open if Redis is down
        }
    }

    private function clearFailedAttempts(string $ip): void
    {
        try {
            Redis::del("auth:failed:{$ip}");
        } catch (\Exception $e) {
            // Non-fatal
        }
    }
}
