<?php

namespace Tests\Feature;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BookingValidationTest extends TestCase
{
    use RefreshDatabase;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2030-01-01 07:00:00', 'Asia/Kolkata'));
        $this->room = Room::query()->create([
            'name' => 'Alpha',
            'capacity' => 4,
            'floor' => '1F',
        ]);

        Route::post('/_tests/bookings/validate', function (StoreBookingRequest $request) {
            return response()->json($request->validated());
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_end_must_be_after_start(): void
    {
        $this->postJson('/_tests/bookings/validate', $this->payload('10:00', '10:00'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('end_time')
            ->assertJsonStructure(['message', 'errors' => ['end_time']]);

        $this->postJson('/_tests/bookings/validate', $this->payload('10:00', '09:00'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('end_time');
    }

    public function test_past_booking_is_rejected(): void
    {
        $response = $this->postJson('/_tests/bookings/validate', [
            'room_id' => $this->room->id,
            'title' => 'Past meeting',
            'booked_by' => 'Test user',
            'start_time' => '2030-01-01T06:00:00',
            'end_time' => '2030-01-01T07:30:00',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('start_time')
            ->assertJsonFragment(['The start time must not be in the past.']);
    }

    public function test_booking_longer_than_four_hours_is_rejected(): void
    {
        $this->postJson('/_tests/bookings/validate', $this->payload('09:00', '13:01'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('end_time');
    }

    public function test_exactly_four_hours_is_allowed(): void
    {
        $this->postJson('/_tests/bookings/validate', $this->payload('09:00', '13:00'))
            ->assertOk();
    }

    public function test_booking_before_opening_time_is_rejected(): void
    {
        $this->postJson('/_tests/bookings/validate', $this->payload('07:59', '08:59'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('start_time');
    }

    public function test_booking_after_closing_time_is_rejected(): void
    {
        $this->postJson('/_tests/bookings/validate', $this->payload('19:00', '20:01'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('end_time');
    }

    public function test_booking_ending_exactly_at_closing_time_is_allowed(): void
    {
        $this->postJson('/_tests/bookings/validate', $this->payload('16:00', '20:00'))
            ->assertOk();
    }

    public function test_booking_cannot_cross_midnight(): void
    {
        $this->postJson('/_tests/bookings/validate', [
            'room_id' => $this->room->id,
            'title' => 'Night meeting',
            'booked_by' => 'Test user',
            'start_time' => '2030-01-02T19:00:00',
            'end_time' => '2030-01-03T01:00:00',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('end_time');
    }

    /**
     * @return array{room_id: int, title: string, booked_by: string, start_time: string, end_time: string}
     */
    private function payload(string $start, string $end): array
    {
        return [
            'room_id' => $this->room->id,
            'title' => 'Team meeting',
            'booked_by' => 'Test user',
            'start_time' => "2030-01-02T{$start}:00",
            'end_time' => "2030-01-02T{$end}:00",
        ];
    }
}
