<?php

namespace App\Exceptions;

use App\Models\Booking;
use RuntimeException;

class BookingConflictException extends RuntimeException
{
    public function __construct(public readonly Booking $conflictingBooking)
    {
        $start = $conflictingBooking->start_time->format('H:i');
        $end = $conflictingBooking->end_time->format('H:i');

        parent::__construct(
            "{$conflictingBooking->title} ({$start} - {$end}) is already booked for this room.",
        );
    }
}
