<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomDirectoryViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2030-01-02 10:30:00', 'Asia/Kolkata'));
        $this->seed();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_room_directory_filters_and_shows_current_usage(): void
    {
        $beta = Room::query()->where('name', 'Beta')->firstOrFail();
        Booking::query()->create([
            'room_id' => $beta->id,
            'title' => 'Planning session',
            'booked_by' => 'Aman',
            'start_time' => '2030-01-02 10:00:00',
            'end_time' => '2030-01-02 11:00:00',
            'status' => BookingStatus::Confirmed,
        ]);

        $this->get('/rooms?floor=1F&capacity=5')
            ->assertOk()
            ->assertSee('Beta')
            ->assertSee('In use until 11:00')
            ->assertDontSee('Alpha')
            ->assertDontSee('Gamma');
    }

    public function test_available_room_card_links_to_a_prefilled_booking_drawer(): void
    {
        $this->get('/rooms/available?date=2030-01-03&start_time=10:00&end_time=11:00&people=5')
            ->assertOk()
            ->assertSee('Book this room')
            ->assertSee('data-start-time="2030-01-03T10:00"', false)
            ->assertSee('data-end-time="2030-01-03T11:00"', false)
            ->assertSee('start_time=2030-01-03T10%3A00', false);
    }
}
