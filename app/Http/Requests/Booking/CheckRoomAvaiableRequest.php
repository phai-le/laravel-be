<?php

namespace App\Http\Requests\Booking;

use App\Base\FormRequest;

class CheckRoomAvaiableRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'roomType' => ['nullable', 'integer'],
            'roomId' => ['nullable', 'integer'],
            'checkin_at' => ['required', 'date_format:Y-m-d H:i', 'after_or_equal:now'],
            'checkout_at' => ['required', 'date_format:Y-m-d H:i', 'after:checkin_at'],
        ];
    }

    /**
     * Custom error messages for the request.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'roomType.integer' => 'Room type must be a valid number.',

            'roomId.integer' => 'Room must be a valid number.',

            'checkin_at.required' => 'Please select a check-in date and time.',
            'checkin_at.date_format' => 'Check-in date format is invalid. Please use YYYY-MM-DD HH:mm.',
            'checkin_at.after_or_equal' => 'Check-in date and time must be today or later. Past times are not allowed.',

            'checkout_at.required' => 'Please select a check-out date and time.',
            'checkout_at.date_format' => 'Check-out date format is invalid. Please use YYYY-MM-DD HH:mm.',
            'checkout_at.after' => 'Check-out date and time must be after the check-in date and time.',
        ];
    }
}
