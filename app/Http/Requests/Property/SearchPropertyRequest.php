<?php

namespace App\Http\Requests\Property;

use App\Base\FormRequest;

class SearchPropertyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed> The validation rules.
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
        ];
    }
}
