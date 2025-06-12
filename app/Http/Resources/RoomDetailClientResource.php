<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomDetailClientResource extends JsonResource
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
            'room_type_id' => $this->room_type_id,
            'price' => $this->roomType->price,
            'thumbnail_path' => asset('storage/' . $this->thumbnail_path),
            'images' => ImageResource::collection($this->images),
            'description' => $this->description,
            'status' => $this->status,
            'properties' => RoomPropertyResource::collection($this->roomType->roomProperties),
        ];
    }
}
