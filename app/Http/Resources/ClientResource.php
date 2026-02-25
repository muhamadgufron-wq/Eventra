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
            'bride_phone'    => $this->bride_phone,
            'groom_phone'    => $this->groom_phone,
            'email'          => $this->email,
            'event_date'     => $this->event_date?->format('Y-m-d'),
            'event_location' => $this->event_location,
            'package_id'     => $this->package_id,
            'package_name'   => $this->package?->name,
            'notes'          => $this->notes,
            'created_at'     => $this->created_at?->toISOString(),
        ];
    }
}
