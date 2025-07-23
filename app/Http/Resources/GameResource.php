<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'access_code' => $this->access_code,
            'qr_code_url' => $this->qr_code_url,
            'openQuestions' => $this->whenLoaded('openQuestions'),
            'liveQuestions' => $this->whenLoaded('liveQuestions'),
            'questions' => $this->whenLoaded('questions', function ($questions) {
                return $questions->map(function ($question) {
                    $question->votesByHost = $question->votesByHost();
                    return $question;
                })->values()->all();
            }),
            'watchers' => $this->whenLoaded('watchers'),
            'votableHost1' => $this->votableHost1,
            'votableHost2' => $this->votableHost2,
            'votableHosts' => $this->votableHosts,
        ];
    }
}
