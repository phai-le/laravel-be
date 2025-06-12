<?php

namespace App\Http\Requests\Room;

use Illuminate\Validation\Rule;
use App\Base\FormRequest;
use App\Enums\RoomStatus;

class SearchRoomRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'room_type_id' => ['nullable', 'integer', 'exists:room_types,id'],
            'status' => ['nullable', 'integer', Rule::enum(RoomStatus::class)],
            'per_page' => ['nullable', 'integer', 'min:3'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
