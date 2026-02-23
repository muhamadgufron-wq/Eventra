<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id'           => ['required', 'exists:clients,id'],
            'invoice_date'        => ['required', 'date'],
            'due_date'            => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'dp_amount'           => ['nullable', 'numeric', 'min:0'],
            'notes'               => ['nullable', 'string'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.package_id'  => ['nullable', 'exists:packages,id'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Invoice harus memiliki minimal 1 item.',
            'items.min'      => 'Invoice harus memiliki minimal 1 item.',
        ];
    }
}
