<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingIndexRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookingController extends Controller
{
    public function index(
        BookingIndexRequest $request,
        BookingService $bookings,
    ): AnonymousResourceCollection {
        $filters = $request->validated();
        $date = $filters['date'] ?? now()->toDateString();
        $roomId = isset($filters['room_id']) ? (int) $filters['room_id'] : null;

        return BookingResource::collection($bookings->forDay($date, $roomId));
    }

    public function store(
        StoreBookingRequest $request,
        BookingService $bookings,
    ): JsonResponse {
        $booking = $bookings->create($request->validated());
        $booking->load('room');

        return (new BookingResource($booking))->response()->setStatusCode(201);
    }

    public function destroy(Booking $booking, BookingService $bookings): JsonResponse
    {
        $cancelled = $bookings->cancel($booking->id)->load('room');

        return response()->json([
            'message' => 'Booking cancelled.',
            'data' => new BookingResource($cancelled),
        ]);
    }
}
