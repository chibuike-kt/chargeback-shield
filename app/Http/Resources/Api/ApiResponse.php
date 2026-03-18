<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
  /**
   * Successful response.
   */
  public static function success(
    mixed $data,
    string $message = 'Success',
    int $status = 200,
    array $meta = []
  ): JsonResponse {
    $payload = [
      'success' => true,
      'message' => $message,
      'data'    => $data,
    ];

    if (!empty($meta)) {
      $payload['meta'] = $meta;
    }

    return response()->json($payload, $status);
  }

  /**
   * Error response.
   */
  public static function error(
    string $message,
    int $status = 400,
    array $errors = []
  ): JsonResponse {
    $payload = [
      'success' => false,
      'message' => $message,
    ];

    if (!empty($errors)) {
      $payload['errors'] = $errors;
    }

    return response()->json($payload, $status);
  }

  /**
   * Validation error response.
   */
  public static function validationError(array $errors): JsonResponse
  {
    return self::error('Validation failed.', 422, $errors);
  }
}
