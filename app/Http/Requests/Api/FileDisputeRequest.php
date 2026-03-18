<?php

namespace App\Http\Requests\Api;

use App\Http\Resources\Api\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FileDisputeRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'transaction_id' => ['required', 'string'],
      'reason_code'    => ['required', 'string', 'max:20'],
      'network'        => ['required', 'string', 'in:visa,mastercard'],
      'filed_at'       => ['nullable', 'date'],
      'notes'          => ['nullable', 'string', 'max:1000'],
    ];
  }

  protected function failedValidation(Validator $validator): void
  {
    throw new HttpResponseException(
      ApiResponse::validationError($validator->errors()->toArray())
    );
  }
}
