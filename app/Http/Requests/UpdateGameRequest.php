<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->currentHost?->show_id === $this->route('game')->show_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_question_id' => ['sometimes', 'nullable', 'uuid', 'exists:questions,id'],
            'status' => ['sometimes', 'string', 'in:upcoming,live,ended'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'access_code' => ['sometimes', 'string', 'max:255'],
            'votable_host_1_id' => ['sometimes', 'uuid', 'exists:hosts,id'],
            'votable_host_2_id' => ['sometimes', 'uuid', 'exists:hosts,id'],
        ];
    }
}
