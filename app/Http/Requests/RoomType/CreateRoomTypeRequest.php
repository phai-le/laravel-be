<?php

namespace App\Http\Requests\RoomType;

use App\Base\FormRequest;
use Illuminate\Validation\Rule;

class CreateRoomTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed> The validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('room_types')->whereNull('deleted_at')],
            'price' => ['required', 'numeric'],

            'properties' => ['required', 'array'],
            'properties.*.property_id' => ['required', 'exists:properties,id', 'distinct'],
            'properties.*.value' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'properties.*.property_id.exists' => 'Property does not exist.',
        ];
    }
}
