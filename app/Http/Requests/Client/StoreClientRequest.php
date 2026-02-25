<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bride_name'     => ['required', 'string', 'max:255'],
            'groom_name'     => ['required', 'string', 'max:255'],
            'bride_phone'    => ['nullable', 'string', 'max:20'],
            'groom_phone'    => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'event_date'     => ['nullable', 'date'],
            'event_location' => ['nullable', 'string', 'max:255'],
            'package_id'     => ['nullable', 'exists:packages,id'],
            'notes'          => ['nullable', 'string'],
        ];
    }
}
