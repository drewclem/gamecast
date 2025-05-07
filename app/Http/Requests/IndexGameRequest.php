<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexGameRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'status' => ['sometimes', 'string', 'in:upcoming,live,ended'],
      'search' => ['sometimes', 'string', 'max:255'],
    ];
  }
}
