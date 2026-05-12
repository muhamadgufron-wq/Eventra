<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id'   => ['sometimes', 'required', 'exists:invoices,id'],
            'amount'       => ['sometimes', 'required', 'numeric', 'min:1'],
            'payment_date' => ['sometimes', 'required', 'date'],
            'method'       => ['sometimes', 'required', 'string', 'in:cash,transfer,qris,other'],
            'notes'        => ['nullable', 'string'],
        ];
    }
}
