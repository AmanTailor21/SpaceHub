<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $floor = $request->query('floor');
        $floor = is_string($floor) ? $floor : '';
        $capacity = $request->query('capacity');
        $capacity = is_numeric($capacity) && (int) $capacity > 0 ? (int) $capacity : null;
        $now = now();

        $rooms = Room::query()
            ->with(['bookings' => function (Relation $query) use ($now) {
                return $query
                    ->where('status', BookingStatus::Confirmed)
                    ->whereDate('start_time', $now->toDateString())
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>', $now)
                    ->orderBy('end_time');
            }])
            ->when($floor !== '', fn ($query) => $query->where('floor', $floor))
            ->when($capacity !== null, fn ($query) => $query->where('capacity', '>=', $capacity))
            ->orderBy('floor')
            ->orderBy('name')
            ->get();
        $floors = Room::query()->select('floor')->distinct()->orderBy('floor')->pluck('floor');

        return view('rooms.index', compact('rooms', 'floors', 'floor', 'capacity'));
    }
}
