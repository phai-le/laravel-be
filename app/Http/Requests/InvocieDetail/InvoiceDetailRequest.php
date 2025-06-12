<?php

namespace App\Http\Requests\InvocieDetail;

use App\Base\FormRequest;

class InvoiceDetailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules()
    {
        return [
            "invoice_details" => ['nullable', 'array'],
            "invoice_details.*.id" => ['nullable', 'numeric', 'exists:invoice_details,id'],
            'invoice_details.*.service_id' => ['required', 'exists:services,id'],
            'invoice_details.*.quantity' => ['required', 'numeric', 'min:1'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'invoice_details.*.service_id.required' => 'The service field is required.',
            'invoice_details.*.service_id.exists' => 'The service selected is not exists.',

            'invoice_details.*.quantity.required' => 'The quantity field is required.',
        ];
    }
}
