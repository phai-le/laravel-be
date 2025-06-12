<?php

namespace App\Http\Requests\Auth;

use App\Base\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:125', Rule::unique('users', 'email')->ignore($this->user()->id),],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($this->user()->id),],
        ];
    }
}
