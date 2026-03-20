<?php

return [

  'paths' => ['api/*'],

  'allowed_methods' => ['GET', 'POST', 'OPTIONS'],

  'allowed_origins' => ['*'],

  'allowed_origins_patterns' => [],

  'allowed_headers' => [
    'Content-Type',
    'X-API-Key',
    'Authorization',
    'Accept',
    'X-Requested-With',
  ],

  'exposed_headers' => [
    'X-RateLimit-Limit',
    'X-RateLimit-Remaining',
    'X-RateLimit-Reset',
  ],

  'max_age' => 3600,

  'supports_credentials' => false,

];
