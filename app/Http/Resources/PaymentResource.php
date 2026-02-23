<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'invoice_id'   => $this->invoice_id,
            'invoice'      => new InvoiceResource($this->whenLoaded('invoice')),
            'amount'       => $this->amount,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'method'       => $this->method,
            'notes'        => $this->notes,
            'created_at'   => $this->created_at?->toISOString(),
        ];
    }
}
