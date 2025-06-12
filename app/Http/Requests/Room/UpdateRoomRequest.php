<?php

namespace App\Http\Requests\Room;

use App\Base\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\RoomStatus;

class UpdateRoomRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'room_type_id' => ['required', 'integer', Rule::exists('room_types', 'id')->whereNull('deleted_at')],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'images_to_remove' => ['nullable', 'array', 'max:20'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'integer', Rule::enum(RoomStatus::class)],
        ];
    }
}
