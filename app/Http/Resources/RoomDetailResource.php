<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class RoomDetailResource extends JsonResource
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
            'room_type_id' => $this->room_type_id,
            'thumbnail_path' => asset('storage/' . $this->thumbnail_path),
            'description' => $this->description,
            'status' => $this->status,
            'images' => ImageResource::collection($this->images),
        ];
    }
}
