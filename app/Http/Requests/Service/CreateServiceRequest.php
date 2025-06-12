<?php

namespace App\Http\Requests\Service;

use App\Base\FormRequest;

class CreateServiceRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'unique:services,name',
                'max:255',
            ],
            'price' => [
                'required',
                'numeric',
            ],
            'status' => [
                'required',
            ]
        ];
    }
}
