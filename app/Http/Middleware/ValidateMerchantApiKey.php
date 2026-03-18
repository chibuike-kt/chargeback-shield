<?php

namespace App\Http\Middleware;

use App\Http\Resources\Api\ApiResponse;
use App\Models\Merchant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateMerchantApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key')
            ?? $request->header('Authorization');

        // Support both "Bearer cs_live_xxx" and raw "cs_live_xxx"
        if ($apiKey && str_starts_with($apiKey, 'Bearer ')) {
            $apiKey = substr($apiKey, 7);
        }

        if (!$apiKey) {
            return ApiResponse::error(
                'API key is required. Pass it as X-API-Key header.',
                401
            );
        }

        $merchant = Merchant::where('api_key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$merchant) {
            return ApiResponse::error(
                'Invalid or inactive API key.',
                401
            );
        }

        // Bind merchant to the request so controllers can access it
        $request->merge(['_authenticated_merchant' => $merchant]);
        $request->attributes->set('merchant', $merchant);

        return $next($request);
    }
}
