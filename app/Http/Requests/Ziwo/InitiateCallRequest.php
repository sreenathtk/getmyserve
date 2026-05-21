<?php

namespace App\Http\Requests\Ziwo;

use Illuminate\Foundation\Http\FormRequest;

class InitiateCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone'       => 'required|string|min:7|max:20',
            'entity_type' => 'nullable|string|in:order,enquiry,assistance_request,customer,provider',
            'entity_id'   => 'nullable|integer|min:1',
        ];
    }
}
