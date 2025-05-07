<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
  public function authorize(): bool
  {
    return $this->user()->currentHost()->exists();
  }

  public function rules(): array
  {
    return [
      'title' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'access_password' => ['nullable', 'string', 'max:255'],
    ];
  }
}
