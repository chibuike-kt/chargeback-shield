<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Resources\Api\ApiResponse;

class TransactionInterceptRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      // Idempotency
      'idempotency_key'      => ['required', 'string', 'min:8', 'max:128'],

      // Card details
      'card_bin'             => ['required', 'string', 'size:6'],
      'card_last4'           => ['required', 'string', 'size:4'],
      'card_country'         => ['nullable', 'string', 'size:2'],

      // Transaction
      'amount'               => ['required', 'integer', 'min:1'],
      'currency'             => ['required', 'string', 'size:3'],

      // Network
      'ip_address'           => ['nullable', 'ip'],
      'ip_country'           => ['nullable', 'string', 'size:2'],
      'ip_city'              => ['nullable', 'string', 'max:100'],

      // Device and session
      'device_fingerprint'   => ['nullable', 'string', 'max:128'],
      'session_token'        => ['nullable', 'string', 'max:128'],
      'session_age_seconds'  => ['nullable', 'integer', 'min:0'],

      // Merchant context
      'merchant_category'    => ['nullable', 'string', 'max:10'],
    ];
  }

  public function messages(): array
  {
    return [
      'card_bin.size'    => 'card_bin must be exactly 6 digits.',
      'card_last4.size'  => 'card_last4 must be exactly 4 digits.',
      'amount.min'       => 'amount must be a positive integer in minor units.',
      'currency.size'    => 'currency must be a 3-letter ISO code (e.g. NGN).',
    ];
  }

  /**
   * Return JSON error instead of redirect on validation failure.
   */
  protected function failedValidation(Validator $validator): void
  {
    throw new HttpResponseException(
      ApiResponse::validationError($validator->errors()->toArray())
    );
  }
}
