<?php

namespace App\Models;

use App\Enums\BookingStatus;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'room_id',
        'title',
        'booked_by',
        'start_time',
        'end_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'status' => BookingStatus::class,
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopeOverlapping(
        Builder $query,
        int $roomId,
        DateTimeInterface $start,
        DateTimeInterface $end,
    ): Builder {
        return $query
            ->where('room_id', $roomId)
            ->where('status', BookingStatus::Confirmed->value)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start);
    }
}
