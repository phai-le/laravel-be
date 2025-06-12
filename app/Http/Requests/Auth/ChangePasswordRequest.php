<?php

namespace App\Http\Requests\Auth;

use App\Base\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'min:6', 'max:255'],
            'new_password' => ['required', 'string', 'min:6', 'max:255', 'confirmed'],
        ];
    }
}
