<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailableRoomsRequest;
use App\Services\BookingService;
use Illuminate\Contracts\View\View;

class AvailableRoomsController extends Controller
{
    public function index(
        AvailableRoomsRequest $request,
        BookingService $bookings,
    ): View {
        $filters = $request->validated();
        $rooms = $bookings->availableRooms(
            $filters['start_time'],
            $filters['end_time'],
            (int) $filters['people'],
        );

        return view('rooms.available', [
            'rooms' => $rooms,
            'date' => $filters['date'],
            'startTime' => substr($filters['start_time'], 11),
            'endTime' => substr($filters['end_time'], 11),
            'people' => $filters['people'],
        ]);
    }
}
