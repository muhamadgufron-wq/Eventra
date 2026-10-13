<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id'   => ['required', 'exists:invoices,id'],
            'amount'       => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'method'       => ['required', new \Illuminate\Validation\Rules\Enum(\App\Enums\PaymentMethod::class)],
            'notes'        => ['nullable', 'string'],
        ];
    }
}
