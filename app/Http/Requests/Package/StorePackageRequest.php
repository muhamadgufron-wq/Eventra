<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['nullable', new \Illuminate\Validation\Rules\Enum(\App\Enums\PackageType::class)],
            'description' => ['nullable', 'string'],
            'services'    => ['nullable', 'array'],
            'services.*.name' => ['required_with:services', 'string'],
            'services.*.qty'  => ['nullable', 'string'],
            'services.*.description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'is_active'   => ['boolean'],
        ];
    }
}
