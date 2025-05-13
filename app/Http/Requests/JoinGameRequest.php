<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JoinGameRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'email' => ['required', 'email', 'max:255'],
      'nickname' => ['required', 'string', 'max:255'],
      'access_password' => [
        'required',
        'string',
        'max:255',
        Rule::exists('games', 'access_code')->where('slug', $this->route('game')->slug),
      ],
    ];
  }
}
