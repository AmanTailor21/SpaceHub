<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2030-01-01 10:00:00', 'Asia/Kolkata'));
        $this->seed();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_schedule_renders_booking_position_and_day_navigation(): void
    {
        $room = Room::query()->where('name', 'Alpha')->firstOrFail();
        app(BookingService::class)->create([
            'room_id' => $room->id,
            'title' => 'Product planning',
            'booked_by' => 'Aman',
            'start_time' => '2030-01-02 09:56',
            'end_time' => '2030-01-02 12:56',
        ]);

        $this->get('/?date=2030-01-02&room_id='.$room->id)
            ->assertOk()
            ->assertSee('Product planning')
            ->assertSee('09:56–12:56', false)
            ->assertSee('data-grid-start="117"', false)
            ->assertSee('data-grid-end="297"', false)
            ->assertSee('date=2030-01-01&amp;room_id='.$room->id, false)
            ->assertSee('Times shown in IST');
    }

    public function test_schedule_shows_an_accessible_mobile_empty_state(): void
    {
        $this->get('/?date=2030-01-02')
            ->assertOk()
            ->assertSee('No bookings for this room.')
            ->assertSee('aria-label="Alpha room schedule"', false)
            ->assertSee('data-open-booking', false);
    }
}
