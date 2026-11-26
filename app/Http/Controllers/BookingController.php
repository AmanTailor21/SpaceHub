<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingIndexRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function index(
        BookingIndexRequest $request,
        BookingService $bookings,
    ): View {
        $filters = $request->validated();
        $date = $filters['date'] ?? $this->dateFromOldBooking() ?? now()->toDateString();
        $oldRoomId = old('room_id');
        $roomId = isset($filters['room_id'])
            ? (int) $filters['room_id']
            : (is_numeric($oldRoomId) ? (int) $oldRoomId : null);
        $rooms = Room::query()->orderBy('floor')->orderBy('name')->get();

        if ($request->query('view') === 'list') {
            return $this->bookingList($request, $rooms, $filters);
        }

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
            'initialRoomId' => $roomId,
            'initialStartTime' => $this->bookingDateTimeQuery($request->query('start_time'), $date),
            'initialEndTime' => $this->bookingDateTimeQuery($request->query('end_time'), $date),
        ]);
    }

    /**
     * @param  Collection<int, Room>  $rooms
     * @param  array{date?: string, room_id?: int|string}  $filters
     */
    private function bookingList(
        BookingIndexRequest $request,
        Collection $rooms,
        array $filters,
    ): View {
        $status = $request->query('status');
        $status = is_string($status) && in_array($status, ['confirmed', 'cancelled'], true)
            ? $status
            : '';
        $bookedBy = $request->query('booked_by');
        $bookedBy = is_string($bookedBy) ? trim($bookedBy) : '';
        $sort = $request->query('sort');
        $sortColumns = [
            'date' => 'start_time',
            'room' => 'room_id',
            'status' => 'status',
            'booked_by' => 'booked_by',
        ];
        $sort = is_string($sort) && isset($sortColumns[$sort]) ? $sort : 'date';
        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';

        $query = Booking::query()
            ->with('room')
            ->when(isset($filters['date']), fn (Builder $query) => $query->whereDate('start_time', $filters['date']))
            ->when(isset($filters['room_id']), fn (Builder $query) => $query->where('room_id', $filters['room_id']))
            ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
            ->when($bookedBy !== '', fn (Builder $query) => $query->where('booked_by', 'like', '%'.$bookedBy.'%'))
            ->orderBy($sortColumns[$sort], $direction);

        return view('bookings.list', [
            'bookings' => $query->paginate(15)->withQueryString(),
            'rooms' => $rooms,
            'filters' => [
                'date' => $filters['date'] ?? '',
                'room_id' => $filters['room_id'] ?? '',
                'status' => $status,
                'booked_by' => $bookedBy,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    private function bookingDateTimeQuery(mixed $value, string $date): ?string
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value)) {
            return null;
        }

        return substr($value, 0, 10) === $date ? $value : null;
    }

    private function dateFromOldBooking(): ?string
    {
        $startTime = old('start_time');

        if (! is_string($startTime) || ! preg_match('/^(\d{4})-(\d{2})-(\d{2})T/', $startTime, $matches)) {
            return null;
        }

        if (! checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1])) {
            return null;
        }

        return substr($startTime, 0, 10);
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
