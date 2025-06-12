<?php

namespace App\Http\Requests\Booking;

use App\Base\FormRequest;
use Illuminate\Validation\Rule;

class IdsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'exists:booking_details,id'],
        ];
    }
}
