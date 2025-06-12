<?php

namespace App\Http\Requests\Booking;

use App\Base\FormRequest;

class CheckOutRoomRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
        ];
    }
}
