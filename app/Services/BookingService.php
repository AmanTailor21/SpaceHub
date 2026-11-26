<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Exceptions\BookingAlreadyCancelledException;
use App\Exceptions\BookingConflictException;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * @param  array{room_id: int|string, title: string, booked_by: string, start_time: string, end_time: string}  $attributes
     */
    public function create(array $attributes): Booking
    {
        return DB::transaction(function () use ($attributes): Booking {
            $room = Room::query()
                ->lockForUpdate()
                ->findOrFail($attributes['room_id']);

            $timezone = (string) config('app.timezone');
            $start = Carbon::parse($attributes['start_time'], $timezone)->setTimezone($timezone);
            $end = Carbon::parse($attributes['end_time'], $timezone)->setTimezone($timezone);

            $conflict = Booking::query()
                ->overlapping($room->id, $start, $end)
                ->first();

            if ($conflict !== null) {
                throw new BookingConflictException($conflict);
            }

            return $room->bookings()->create([
                'title' => $attributes['title'],
                'booked_by' => $attributes['booked_by'],
                'start_time' => $start,
                'end_time' => $end,
                'status' => BookingStatus::Confirmed,
            ]);
        });
    }

    public function cancel(int $bookingId): Booking
    {
        return DB::transaction(function () use ($bookingId): Booking {
            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($bookingId);

            if ($booking->status === BookingStatus::Cancelled) {
                throw new BookingAlreadyCancelledException;
            }

            $booking->status = BookingStatus::Cancelled;
            $booking->save();

            return $booking;
        });
    }

    /**
     * @return Collection<int, Booking>
     */
    public function forDay(string $date, ?int $roomId = null): Collection
    {
        $day = Carbon::parse($date, (string) config('app.timezone'))->startOfDay();
        $nextDay = $day->copy()->addDay();

        return Booking::query()
            ->with('room')
            ->where('start_time', '>=', $day)
            ->where('start_time', '<', $nextDay)
            ->when($roomId !== null, fn ($query) => $query->where('room_id', $roomId))
            ->orderBy('start_time')
            ->get();
    }
}
