<?php

namespace App\Http\Requests\Contact;

use App\Base\FormRequest;

class GetContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|integer',
            'guest_name' => 'nullable|string',
            'guest_email' => 'nullable|email',
            'title' => 'required|string',
            'content' => 'required|string',
            'content_reply' => 'nullable|string',
        ];
    }

    /**
     * Custom validation rules
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $userId = $this->input('user_id');
            $guestName = $this->input('guest_name');
            $guestEmail = $this->input('guest_email');

            if (empty($userId)) {
                if (empty($guestName) || empty($guestEmail)) {
                    $validator->errors()->add('guest_info', 'If there is no user_id, you must provide both guest_name and guest_email.');
                }
            }
        });
    }
}
