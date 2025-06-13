<?php

namespace App\Http\Requests\Admin;

use App\Base\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
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
        $adminId = $this->route('id') ?? $this->route('admin_account');
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email,' . $adminId],
            'phone' => ['required', 'string', 'regex:/^0[0-9]{9,}$/', 'unique:admins,phone,' . $adminId],
        ];
    }
}
