<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $responseTime = (int) ((microtime(true) - $startTime) * 1000);
        $merchant     = $request->attributes->get('merchant');
        $statusCode   = $response->getStatusCode();

        // Log to database asynchronously — don't slow down the request
        try {
            DB::table('api_request_logs')->insert([
                'merchant_id'      => $merchant?->id,
                'method'           => $request->method(),
                'path'             => $request->path(),
                'status_code'      => $statusCode,
                'ip_address'       => $request->ip(),
                'response_time_ms' => $responseTime,
                'user_agent'       => substr($request->userAgent() ?? '', 0, 255),
                'created_at'       => now(),
            ]);
        } catch (\Exception $e) {
            // Non-fatal — never let logging break the API
            Log::warning('API request logging failed: ' . $e->getMessage());
        }

        // Flag suspicious activity
        $this->checkSuspiciousActivity($request, $statusCode, $merchant);

        return $response;
    }

    private function checkSuspiciousActivity(
        Request $request,
        int $statusCode,
        $merchant
    ): void {
        try {
            $ip = $request->ip();

            // Track 401s per IP — brute force detection
            if ($statusCode === 401) {
                $key    = "suspicious:401:{$ip}";
                $config = config('security.suspicious');

                $count = \Illuminate\Support\Facades\Redis::incr($key);
                \Illuminate\Support\Facades\Redis::expire($key, $config['unauthorized_window']);

                if ($count >= $config['unauthorized_threshold']) {
                    Log::warning('Suspicious: excessive 401s from IP', [
                        'ip'    => $ip,
                        'count' => $count,
                        'path'  => $request->path(),
                    ]);
                }
            }

            // Track declined transactions per merchant
            if ($merchant && $statusCode === 200) {
                // Checked inside response body — skip for now,
                // decline tracking happens in InterceptTransaction action
            }
        } catch (\Exception $e) {
            // Non-fatal
        }
    }
}
