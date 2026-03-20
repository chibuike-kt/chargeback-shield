<?php

return [

  /*
    |--------------------------------------------------------------------------
    | API Rate Limits
    |--------------------------------------------------------------------------
    |
    | All limits are per merchant per time window.
    | Limits use Redis sliding windows for accuracy.
    |
    */

  'rate_limits' => [

    // Transaction interception — core endpoint
    'transaction_intercept' => [
      'requests' => 300,   // 300 requests
      'window'   => 60,    // per 60 seconds
    ],

    // Evidence and dispute endpoints
    'standard_api' => [
      'requests' => 120,
      'window'   => 60,
    ],

    // Auth endpoints — login and register
    'auth' => [
      'requests' => 10,
      'window'   => 60,
    ],

    // Global per-IP fallback
    'global_ip' => [
      'requests' => 500,
      'window'   => 60,
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Suspicious Activity Thresholds
    |--------------------------------------------------------------------------
    */

  'suspicious' => [
    // How many 401s from same IP before flagging
    'unauthorized_threshold' => 20,
    'unauthorized_window'    => 300, // 5 minutes

    // How many declined transactions before flagging
    'decline_threshold' => 50,
    'decline_window'    => 3600, // 1 hour
  ],

  /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    */

  'headers' => [
    'X-Frame-Options'           => 'DENY',
    'X-Content-Type-Options'    => 'nosniff',
    'X-XSS-Protection'          => '1; mode=block',
    'Referrer-Policy'           => 'strict-origin-when-cross-origin',
    'Permissions-Policy'        => 'camera=(), microphone=(), geolocation=()',
    'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
  ],

];
