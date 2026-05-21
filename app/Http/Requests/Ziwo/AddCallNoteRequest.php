<?php

namespace App\Http\Requests\Ziwo;

use Illuminate\Foundation\Http\FormRequest;

class AddCallNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => 'required|string|max:2000',
        ];
    }
}
