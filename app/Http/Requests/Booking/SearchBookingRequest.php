<?php

namespace App\Http\Requests\Booking;

use App\Base\FormRequest;

class SearchBookingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'guest_name' => ['nullable', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'guest_phone' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'integer'],
            'created_from' => ['nullable', 'date_format:Y-m-d H:i', 'before_or_equal:created_to'],
            'created_to' => ['nullable', 'date_format:Y-m-d H:i', 'after_or_equal:created_from'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'max:100'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
