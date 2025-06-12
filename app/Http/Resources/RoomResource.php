<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Http\Resources\ImageResource;

class RoomResource extends JsonResource
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
            'price' => $this->roomType->price,
            'thumbnail_path' => asset('storage/' . $this->thumbnail_path),
            'status' => $this->status,
        ];
    }
}
