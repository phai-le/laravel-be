<?php

namespace App\Http\Requests\Dashboard;

use App\Base\FormRequest;
use Illuminate\Validation\Rule;

class RevenueRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['nullable', Rule::in('day', 'week', 'month')],
        ];
    }
}
