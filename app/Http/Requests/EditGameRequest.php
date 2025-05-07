<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditGameRequest extends FormRequest
{
  public function authorize(): bool
  {
    return $this->user()->currentHost()->exists();
  }

  public function rules(): array
  {
    return [];
  }
}
