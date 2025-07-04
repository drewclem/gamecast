<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVoteRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    $game = $this->route('game');
    $question = $this->route('question');

    // Check if the question belongs to this game
    if ($question->game_id !== $game->id) {
      return false;
    }

    // Check if the watcher has already voted
    $watcherId = session('watcher_' . $game->id);
    if (!$watcherId) {
      return false;
    }

    return !$question->votes()
      ->where('watcher_id', $watcherId)
      ->exists();
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'host_id' => [
        'required',
        'uuid',
        'exists:hosts,id',
        function ($attribute, $value, $fail) {
          $game = $this->route('game');
          if (!$game->show->hosts()->where('id', $value)->exists()) {
            $fail('The selected host is not valid for this game.');
          }
        },
      ],
    ];
  }

  /**
   * Get custom messages for validator errors.
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'host_id.required' => 'You must select a host to vote for.',
      'host_id.uuid' => 'Invalid host ID.',
      'host_id.exists' => 'The selected host is not valid for this game.',
    ];
  }
}
