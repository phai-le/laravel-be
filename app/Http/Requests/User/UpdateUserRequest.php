<?php

namespace App\Http\Requests\User;

use App\Base\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('id') ?? $this->route('user');
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $userId],
            'phone' => ['required', 'string', 'regex:/^0[0-9]{9,}$/', 'unique:users,phone,' . $userId],
            'status' => ['required', 'integer', 'in:1,2']
        ];
    }

}
