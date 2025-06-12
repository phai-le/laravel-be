<?php

namespace App\Http\Requests\RoomType;

use App\Base\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('room_types')->ignore($this->route('room_type'))->whereNull('deleted_at')],
            'price' => ['required', 'numeric'],

            'properties' => ['nullable', 'array'],
            'properties.*.property_id' => ['required', 'exists:properties,id', 'distinct'],
            'properties.*.value' => ['required', 'string', 'max:255'],
        ];
    }
}
