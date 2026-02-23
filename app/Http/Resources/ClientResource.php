<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'bride_name'     => $this->bride_name,
            'groom_name'     => $this->groom_name,
            'phone'          => $this->phone,
            'email'          => $this->email,
            'event_date'     => $this->event_date?->format('Y-m-d'),
            'event_location' => $this->event_location,
            'notes'          => $this->notes,
            'created_at'     => $this->created_at?->toISOString(),
        ];
    }
}
