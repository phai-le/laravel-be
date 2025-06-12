<?php

namespace App\Http\Requests\Booking;

use App\Base\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            // 'check_in' => ['nullable', 'date_format:Y-m-d H:i', 'after_or_equal:today'],
            // 'check_out' => ['nullable', 'date_format:Y-m-d H:i', 'after:check_in'],
            'deposit' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'total_payment' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'integer'],

            'booking_details' => ['required', 'array'],
            'booking_details.*.id' => ['nullable', 'integer', 'exists:booking_details,id'],
            'booking_details.*.room_id' => ['required', 'integer', 'exists:rooms,id', 'distinct'],
            'booking_details.*.checkin_at' => ['required', 'date_format:Y-m-d H:i', 'after_or_equal:today'],
            'booking_details.*.checkout_at' => ['required', 'date_format:Y-m-d H:i', 'after:checkin_at'],
        ];
    }

    public function messages()
    {
        return [
            // Booking details array
            'booking_details.required' => 'You must provide at least one booking detail.',

            // booking_details.*.id
            'booking_details.*.id.integer' => 'Invalid booking detail ID.',
            'booking_details.*.id.exists' => 'Selected booking detail does not exist.',

            // booking_details.*.room_id
            'booking_details.*.room_id.required' => 'Please select a room.',
            'booking_details.*.room_id.integer' => 'Invalid room selected.',
            'booking_details.*.room_id.exists' => 'The selected room does not exist.',
            'booking_details.*.room_id.distinct' => 'Duplicate rooms are not allowed.',

            // booking_details.*.checkin_at
            'booking_details.*.checkin_at.required' => 'Please select a check-in date and time.',
            'booking_details.*.checkin_at.date_format' => 'Check-in date format is invalid. Please use YYYY-MM-DD HH:mm.',
            'booking_details.*.checkin_at.after_or_equal' => 'Check-in date and time must be today or later.',

            // booking_details.*.checkout_at
            'booking_details.*.checkout_at.required' => 'Please select a check-out date and time.',
            'booking_details.*.checkout_at.date_format' => 'Check-out date format is invalid. Please use YYYY-MM-DD HH:mm.',
            'booking_details.*.checkout_at.after' => 'Check-out date and time must be after the check-in date and time.',

            // Main fields
            'user_id.integer' => 'Invalid user ID.',
            'guest_name.required' => 'Guest name is required.',
            'guest_name.string' => 'Guest name must be a valid string.',
            'guest_name.max' => 'Guest name may not be greater than 255 characters.',

            'guest_email.required' => 'Guest email is required.',
            'guest_email.email' => 'Guest email must be a valid email address.',
            'guest_email.max' => 'Guest email may not be greater than 255 characters.',

            'guest_phone.required' => 'Guest phone number is required.',
            'guest_phone.string' => 'Guest phone number must be a valid string.',
            'guest_phone.max' => 'Guest phone number may not be greater than 20 characters.',

            'deposit.numeric' => 'Deposit amount must be a valid number.',
            'deposit.min' => 'Deposit amount must be at least 0.',

            'note.string' => 'Note must be a valid string.',

            'total_payment.numeric' => 'Total payment must be a valid number.',
            'total_payment.min' => 'Total payment must be at least 0.',

            'status.integer' => 'Invalid booking status.',
        ];
    }
}
