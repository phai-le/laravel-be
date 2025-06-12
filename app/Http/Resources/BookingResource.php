<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'guest_phone' => $this->guest_phone,
            'check_in' => $this->check_in ? Carbon::parse($this->check_in)->format('Y-m-d H:i') : null,
            'check_out' => $this->check_out ? Carbon::parse($this->check_out)->format('Y-m-d H:i') : null,
            'note' => $this->note,
            'deposit' => $this->deposit,
            'total_payment' => $this->total_payment,
            'status' => $this->status,
            'created_at' =>  $this->created_at ? Carbon::parse($this->created_at)->format('Y-m-d H:i') : null,
            'booking_details' => BookingDetailResource::collection($this->bookingDetails),
        ];
    }
}
