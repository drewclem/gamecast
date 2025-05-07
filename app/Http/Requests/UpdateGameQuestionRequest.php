<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameQuestionRequest extends FormRequest
{
  public function authorize(): bool
  {
    return $this->user()->currentHost?->show_id === $this->route('game')->show_id;
  }

  public function rules(): array
  {
    return [
      'question' => ['sometimes', 'string', 'max:255'],
      'status' => ['sometimes', 'string', 'in:active,inactive'],
      'is_active' => ['sometimes', 'boolean'],
    ];
  }
}
