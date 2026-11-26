<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingsListViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2030-01-01 07:00:00', 'Asia/Kolkata'));
        $this->seed();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_bookings_list_filters_and_sorts_reservations(): void
    {
        $alpha = Room::query()->where('name', 'Alpha')->firstOrFail();
        $beta = Room::query()->where('name', 'Beta')->firstOrFail();
        $service = app(BookingService::class);
        $service->create([
            'room_id' => $alpha->id,
            'title' => 'Design review',
            'booked_by' => 'Aman Tailor',
            'start_time' => '2030-01-02 10:00',
            'end_time' => '2030-01-02 11:00',
        ]);
        $service->create([
            'room_id' => $beta->id,
            'title' => 'Team check-in',
            'booked_by' => 'Mia',
            'start_time' => '2030-01-02 11:00',
            'end_time' => '2030-01-02 12:00',
        ]);

        $this->get('/?view=list&booked_by=Aman&status=confirmed&sort=date&direction=asc')
            ->assertOk()
            ->assertSee('Design review')
            ->assertSee('Aman Tailor')
            ->assertDontSee('Team check-in')
            ->assertSee('Bookings')
            ->assertSee('Open calendar');
    }
}
