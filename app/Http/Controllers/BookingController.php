<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingIndexRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function index(
        BookingIndexRequest $request,
        BookingService $bookings,
    ): View {
        $filters = $request->validated();
        $date = $filters['date'] ?? now()->toDateString();
        $roomId = isset($filters['room_id']) ? (int) $filters['room_id'] : null;
        $rooms = Room::query()->orderBy('floor')->orderBy('name')->get();
        $dayBookings = $bookings->forDay($date, $roomId)->groupBy('room_id');
        $day = Carbon::parse($date, (string) config('app.timezone'));
        $now = now();
        $nowOffset = $date === $now->toDateString()
            ? max(0, min(720, ($now->hour * 60) + $now->minute - 480))
            : null;

        return view('bookings.index', [
            'rooms' => $rooms,
            'bookingsByRoom' => $dayBookings,
            'selectedDate' => $date,
            'selectedRoomId' => $roomId,
            'previousDate' => $day->copy()->subDay()->toDateString(),
            'nextDate' => $day->copy()->addDay()->toDateString(),
            'isToday' => $date === $now->toDateString(),
            'nowOffset' => $nowOffset,
            'timeLabels' => range(8, 20),
        ]);
    }

    public function store(
        StoreBookingRequest $request,
        BookingService $bookings,
    ): RedirectResponse {
        $booking = $bookings->create($request->validated());

        return redirect()
            ->route('bookings.index', ['date' => $booking->start_time->toDateString()])
            ->with('success', 'Booking confirmed.');
    }

    public function destroy(int $booking, BookingService $bookings): RedirectResponse
    {
        $cancelled = $bookings->cancel($booking);

        return redirect()
            ->route('bookings.index', ['date' => $cancelled->start_time->toDateString()])
            ->with('success', 'Booking cancelled.');
    }
}
