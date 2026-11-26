<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingEndpointsTest extends TestCase
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

    public function test_api_creates_lists_and_cancels_bookings(): void
    {
        $created = $this->postJson('/api/bookings', $this->payload($this->alpha, '10:00', '11:00'))
            ->assertCreated()
            ->assertJsonPath('data.title', 'Team stand-up')
            ->assertJsonPath('data.room', 'Alpha')
            ->assertJsonPath('data.status', 'confirmed');

        $id = $created->json('data.id');

        $this->getJson('/api/bookings?date=2030-01-02')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->getJson('/api/bookings?date=2030-01-02&room_id='.$this->beta->id)
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->deleteJson("/api/bookings/{$id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        $this->deleteJson("/api/bookings/{$id}")
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['booking']]);

        $this->assertDatabaseHas('bookings', [
            'id' => $id,
            'status' => BookingStatus::Cancelled->value,
        ]);
    }

    public function test_api_conflict_is_a_409_with_the_existing_booking_details(): void
    {
        $this->postJson('/api/bookings', $this->payload($this->alpha, '10:00', '11:00'))
            ->assertCreated();

        $this->postJson('/api/bookings', $this->payload($this->alpha, '10:30', '11:30'))
            ->assertConflict()
            ->assertJsonStructure(['message', 'errors' => ['start_time']])
            ->assertJsonFragment(['Team stand-up (10:00 - 11:00) is already booked for this room.']);
    }

    public function test_api_validation_errors_have_a_consistent_json_shape(): void
    {
        $this->postJson('/api/bookings', $this->payload($this->alpha, '10:00', '09:00'))
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['end_time']]);
    }

    public function test_web_can_create_and_cancel_a_booking(): void
    {
        $this->from('/')->post('/bookings', $this->payload($this->alpha, '10:00', '11:00'))
            ->assertRedirect('/?date=2030-01-02')
            ->assertSessionHas('success', 'Booking confirmed.');

        $booking = Booking::query()->firstOrFail();

        $this->delete("/bookings/{$booking->id}")
            ->assertRedirect('/?date=2030-01-02')
            ->assertSessionHas('success', 'Booking cancelled.');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => BookingStatus::Cancelled->value,
        ]);
    }

    public function test_web_booking_form_shows_validation_messages(): void
    {
        $this->from('/')
            ->post('/bookings', $this->payload($this->alpha, '10:00', '09:00'))
            ->assertRedirect('/')
            ->assertSessionHasErrors('end_time');
    }

    public function test_web_conflict_restores_the_submitted_day_and_reopens_the_booking_drawer(): void
    {
        $this->post('/bookings', $this->payload($this->alpha, '10:00', '11:00'))
            ->assertRedirect('/?date=2030-01-02');

        $this->from('/')
            ->post('/bookings', $this->payload($this->alpha, '10:30', '11:30'))
            ->assertRedirect('/')
            ->assertSessionHasErrors('start_time');

        $this->get('/')
            ->assertOk()
            ->assertSee('data-open-on-load="true"', false)
            ->assertSee('Team stand-up (10:00 - 11:00) is already booked for this room.')
            ->assertSee('date=2030-01-01&amp;room_id='.$this->alpha->id, false)
            ->assertSee('value="2030-01-02T10:30:00"', false)
            ->assertSee('value="2030-01-02T11:30:00"', false);
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
