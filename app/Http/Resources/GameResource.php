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
            'questions' => $this->whenLoaded('questions'),
            'watchers' => $this->whenLoaded('watchers'),
        ];
    }
}
