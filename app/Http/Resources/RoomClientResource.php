<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomClientResource extends JsonResource
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
            'name' => $this->name,
            'room_type' => $this->roomType->name,
            'thumbnail_path' => asset('storage/' . $this->thumbnail_path),
            'properties' => RoomPropertyResource::collection($this->roomType->roomProperties->take(3)),
        ];
    }
}
