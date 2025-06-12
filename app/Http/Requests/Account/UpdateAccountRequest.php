<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:125', 'unique:users,email', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullbale', 'string', 'max:20'],
            'password' => ['nullbale', 'string', 'min:6', 'max:255', 'confirmed'],
        ];
    }
}
