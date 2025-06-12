<?php

namespace App\Http\Requests\Property;

use App\Base\FormRequest;

class CreatePropertyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed> The validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:properties,name', 'max:255'],

            'per_page' => ['integer', 'min:10', 'max:100'],
        ];
    }
}
