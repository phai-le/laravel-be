<?php

namespace App\Http\Requests\RoomType;

use App\Base\FormRequest;

class SearchRoomTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
        ];
    }
}
