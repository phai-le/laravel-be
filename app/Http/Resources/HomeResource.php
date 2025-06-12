<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
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
            'price' => $this->price,
            'properties' => RoomPropertyResource::collection($this->roomPropertiesLimited),
            'image' => $this->rooms->first()?->thumbnail_path ? asset('storage/' . $this->rooms->first()->thumbnail_path) : null,
        ];
    }
}
