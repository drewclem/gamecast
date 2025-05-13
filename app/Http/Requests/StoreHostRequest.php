<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHostRequest extends FormRequest
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
      'email' => ['required', 'email', 'max:255'],
      'avatar' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png'], // 2MB max
      'color' => ['nullable', 'string', 'max:255'],
    ];
  }
}
