<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event' => new EventResource($this->event),
            'attendee' => new AttendeeResource($this->attendee),
            'status' => $this->status,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
