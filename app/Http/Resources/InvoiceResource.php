<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'invoice_number'   => $this->invoice_number,
            'client_id'        => $this->client_id,
            'client'           => new ClientResource($this->whenLoaded('client')),
            'invoice_date'     => $this->invoice_date?->format('Y-m-d'),
            'due_date'         => $this->due_date?->format('Y-m-d'),
            'total'            => $this->total,
            'dp_amount'        => $this->dp_amount,
            'remaining_amount' => $this->remaining_amount,
            'status'           => $this->status instanceof \App\Enums\InvoiceStatus
                                    ? $this->status->value
                                    : $this->status,
            'notes'            => $this->notes,
            'items'            => InvoiceItemResource::collection($this->whenLoaded('items')),
            'payments'         => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at'       => $this->created_at?->toISOString(),
        ];
    }
}
