<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHostRequest extends FormRequest
{
  public function authorize(): bool
  {
    $show = $this->route('show');
    return $show && $show->user_id === $this->user()->id;
  }

  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'avatar' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png'], // New file upload
      'color' => ['nullable', 'string', 'max:255'],
    ];
  }
}
