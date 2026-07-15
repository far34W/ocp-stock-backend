<?php

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_location'   => ['required', 'string', 'max:255'],
            'from_location' => ['nullable', 'string', 'max:255'],
            'person_name'   => ['nullable', 'string', 'max:255'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'notes'         => ['nullable', 'string'],
        ];
    }
}
