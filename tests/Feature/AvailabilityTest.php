<?php

namespace Tests\Feature;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    use RefreshDatabase;

    private Room $alpha;

    private Room $beta;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2030-01-01 07:00:00', 'Asia/Kolkata'));
        $this->seed();
        $this->alpha = Room::query()->where('name', 'Alpha')->firstOrFail();
        $this->beta = Room::query()->where('name', 'Beta')->firstOrFail();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_availability_filters_by_capacity_and_active_conflicts(): void
    {
        $this->postJson('/api/bookings', $this->payload($this->beta, '10:00', '11:00'))
            ->assertCreated();

        $response = $this->getJson('/api/rooms/available?date=2030-01-02&start_time=10:00&end_time=11:00&people=5')
            ->assertOk()
            ->assertJsonCount(4, 'data');

        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertSame(['Delta', 'Gamma', 'Epsilon', 'Zeta'], $names);
    }

    public function test_back_to_back_slot_is_available_and_cancelled_booking_does_not_block(): void
    {
        $created = $this->postJson('/api/bookings', $this->payload($this->beta, '10:00', '11:00'))
            ->assertCreated();

        $this->getJson('/api/rooms/available?date=2030-01-02&start_time=11:00&end_time=12:00&people=6')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Beta']);

        $this->deleteJson('/api/bookings/'.$created->json('data.id'))->assertOk();

        $this->getJson('/api/rooms/available?date=2030-01-02&start_time=10:00&end_time=11:00&people=6')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Beta']);
    }

    public function test_availability_validation_returns_field_errors(): void
    {
        $this->getJson('/api/rooms/available?date=2030-01-02&start_time=13:00&end_time=12:00&people=2')
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['end_time']]);
    }

    public function test_web_availability_search_shows_matching_rooms(): void
    {
        $this->get('/rooms/available?date=2030-01-02&start_time=10:00&end_time=11:00&people=5')
            ->assertOk()
            ->assertSee('Available rooms')
            ->assertSee('Beta')
            ->assertSee('Zeta')
            ->assertDontSee('Alpha');
    }

    /**
     * @return array{room_id: int, title: string, booked_by: string, start_time: string, end_time: string}
     */
    private function payload(Room $room, string $start, string $end): array
    {
        return [
            'room_id' => $room->id,
            'title' => 'Team stand-up',
            'booked_by' => 'Aman',
            'start_time' => "2030-01-02T{$start}:00",
            'end_time' => "2030-01-02T{$end}:00",
        ];
    }
}
