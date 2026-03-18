<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'company_name' => ['required', 'string', 'min:2', 'max:100'],
      'email'        => ['required', 'email', 'max:255', 'unique:merchants,email'],
      'password'     => ['required', 'string', 'min:8', 'confirmed'],
    ];
  }

  public function messages(): array
  {
    return [
      'email.unique'           => 'An account with this email already exists.',
      'password.confirmed'     => 'Password confirmation does not match.',
      'company_name.required'  => 'Company name is required.',
    ];
  }
}
