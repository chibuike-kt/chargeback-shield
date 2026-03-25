<?php

namespace App\Http\Middleware;

use App\Http\Resources\Api\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class RateLimitApiRequests
{
    /**
     * Rate limit using Redis sorted sets as exact sliding windows.
     * Same pattern as the velocity scorer in Node.js.
     */
    public function handle(Request $request, Closure $next, string $limitType = 'standard_api'): Response
    {
        $limits = config("security.rate_limits.{$limitType}");

        if (!$limits) {
            return $next($request);
        }

        $merchant   = $request->attributes->get('merchant');
        $identifier = $merchant
            ? "ratelimit:{$limitType}:merchant:{$merchant->id}"
            : "ratelimit:{$limitType}:ip:{$request->ip()}";

        $allowed  = $limits['requests'];
        $windowMs = $limits['window'] * 1000;
        $now      = (int) (microtime(true) * 1000);
        $cutoff   = $now - $windowMs;

        try {
            // Use individual commands instead of pipeline — Predis compatible
            Redis::zadd($identifier, $now, (string) $now);
            Redis::zremrangebyscore($identifier, '-inf', $cutoff);
            $count = (int) Redis::zcard($identifier);
            Redis::expire($identifier, $limits['window'] + 10);

            $remaining = max(0, $allowed - $count);
            $resetAt   = $now + $windowMs;
        } catch (\Exception $e) {
            // Fail open — don't block legitimate traffic if Redis is down
            return $next($request);
        }

        if ($count > $allowed) {
            $retryAfter = ceil($limits['window']);

            return ApiResponse::error(
                'Too many requests. Slow down and try again shortly.',
                429,
                [
                    'retry_after' => max(1, $retryAfter),
                    'limit'       => $allowed,
                    'window'      => "{$limits['window']} seconds",
                ]
            )->withHeaders([
                'Retry-After'           => max(1, $retryAfter),
                'X-RateLimit-Limit'     => $allowed,
                'X-RateLimit-Remaining' => 0,
                'X-RateLimit-Reset'     => (int) ($resetAt / 1000),
            ]);
        }

        $response = $next($request);
        $response->headers->set('X-RateLimit-Limit',     $allowed);
        $response->headers->set('X-RateLimit-Remaining', $remaining);
        $response->headers->set('X-RateLimit-Reset',     (int) ($resetAt / 1000));

        return $response;
    }
}
