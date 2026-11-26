<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * @return array{
     *     id: int,
     *     room_id: int,
     *     room: string|null,
     *     title: string,
     *     booked_by: string,
     *     start_time: string,
     *     end_time: string,
     *     status: string
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_id' => $this->room_id,
            'room' => $this->room?->name,
            'title' => $this->title,
            'booked_by' => $this->booked_by,
            'start_time' => $this->start_time->toIso8601String(),
            'end_time' => $this->end_time->toIso8601String(),
            'status' => $this->status->value,
        ];
    }
}
