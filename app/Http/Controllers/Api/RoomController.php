<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvailableRoomsRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return RoomResource::collection(
            Room::query()->orderBy('floor')->orderBy('name')->get(),
        );
    }

    public function available(
        AvailableRoomsRequest $request,
        BookingService $bookings,
    ): AnonymousResourceCollection {
        $filters = $request->validated();

        return RoomResource::collection($bookings->availableRooms(
            $filters['start_time'],
            $filters['end_time'],
            (int) $filters['people'],
        ));
    }
}
