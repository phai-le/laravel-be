<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResources extends JsonResource
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
            'user_id' => $this->user_id,
            'guest_name' => $this->guest_name,
            'guest_email' => $this->guest_email,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'content_reply' => $this->content_reply,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
