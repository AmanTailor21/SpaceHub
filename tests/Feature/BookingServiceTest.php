<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Exceptions\BookingAlreadyCancelledException;
use App\Exceptions\BookingConflictException;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $service;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2030-01-01 07:00:00', 'Asia/Kolkata'));
        $this->service = app(BookingService::class);
        $this->room = Room::query()->create([
            'name' => 'Alpha',
            'capacity' => 4,
            'floor' => '1F',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public static function overlappingRanges(): array
    {
        return [
            'overlapping start' => ['09:30', '10:30'],
            'overlapping end' => ['10:30', '11:30'],
            'fully inside' => ['10:15', '10:45'],
            'fully containing' => ['09:30', '11:30'],
            'identical' => ['10:00', '11:00'],
        ];
    }

    #[DataProvider('overlappingRanges')]
    public function test_overlapping_ranges_are_rejected(string $start, string $end): void
    {
        $this->createBooking($this->room, '10:00', '11:00', 'Planning session');

        try {
            $this->createBooking($this->room, $start, $end);
            $this->fail('An overlapping booking should have been rejected.');
        } catch (BookingConflictException $exception) {
            $this->assertStringContainsString('Planning session', $exception->getMessage());
            $this->assertStringContainsString('10:00 - 11:00', $exception->getMessage());
        }
    }

    public function test_back_to_back_bookings_before_and_after_are_allowed(): void
    {
        $this->createBooking($this->room, '10:00', '11:00');
        $this->createBooking($this->room, '09:00', '10:00');
        $this->createBooking($this->room, '11:00', '12:00');

        $this->assertDatabaseCount('bookings', 3);
    }

    public function test_same_time_in_a_different_room_is_allowed(): void
    {
        $otherRoom = Room::query()->create([
            'name' => 'Beta',
            'capacity' => 6,
            'floor' => '1F',
        ]);

        $this->createBooking($this->room, '10:00', '11:00');
        $this->createBooking($otherRoom, '10:00', '11:00');

        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_cancelled_booking_does_not_block_its_time(): void
    {
        $booking = $this->createBooking($this->room, '10:00', '11:00');
        $this->service->cancel($booking->id);

        $replacement = $this->createBooking($this->room, '10:00', '11:00');

        $this->assertSame(BookingStatus::Confirmed, $replacement->status);
    }

    public function test_booking_can_be_cancelled_only_once(): void
    {
        $booking = $this->createBooking($this->room, '10:00', '11:00');

        $this->assertSame(BookingStatus::Cancelled, $this->service->cancel($booking->id)->status);
        $this->expectException(BookingAlreadyCancelledException::class);

        $this->service->cancel($booking->id);
    }

    private function createBooking(
        Room $room,
        string $start,
        string $end,
        string $title = 'Team meeting',
    ): Booking {
        return $this->service->create([
            'room_id' => $room->id,
            'title' => $title,
            'booked_by' => 'Test user',
            'start_time' => "2030-01-01 {$start}:00",
            'end_time' => "2030-01-01 {$end}:00",
        ]);
    }
}
